<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AllTasksDataTable;
use App\Event;
use App\EventAttendee;
use App\Events\TaskEvent;
use App\Events\TaskReminderEvent;
use App\FileStorage;
use App\GoogleAccount;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Tasks\StoreTask;
use App\Notifications\TaskReminder;
use App\Pinned;
use App\TaskRequest;
use App\Project;
use App\ProjectMember;
use App\ProjectMilestone;
use App\Services\Google;
use App\Task;
use App\TaskboardColumn;
use App\TaskCategory;
use App\TaskFile;
use App\TaskLabel;
use App\TaskLabelList;
use App\TaskRequestFile;
use App\TaskUser;
use App\Traits\ProjectProgress;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class ManageAllTasksController extends AdminBaseController
{
    use ProjectProgress;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tasks';
        $this->pageIcon = 'ti-layout-list-thumb';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('tasks', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index(AllTasksDataTable $dataTable)
    {
        $this->projects = Project::all();
        $this->milestones = ProjectMilestone::all();
        $this->clients = User::allClients();
        $this->employees = User::allEmployees();
        $this->taskCategories = TaskCategory::all();
        $this->taskBoardStatus = TaskboardColumn::all();
        $this->startDate = Carbon::today()->subDays(15)->format($this->global->date_format);
        $this->endDate = Carbon::today()->addDays(15)->format($this->global->date_format);
        $this->taskLabels       = TaskLabelList::all();

        // return view('admin.tasks.index', $this->data);
        return $dataTable->render('admin.tasks.index', $this->data);
    }

    public function edit(Request $request, $id)
    {
        $this->task = Task::with('users', 'label')->findOrFail($id)->withCustomFields();
        $this->fields = $this->task->getCustomFieldGroupsWithFields()->fields;
        $this->task_request_file = TaskRequestFile::where('task_id', $this->task->task_request_id )->get();
        $this->type = ($request->has('type')) ? $request->type : '';

        $this->labelIds         = $this->task->label->pluck('label_id')->toArray();
        $this->projects        = Project::all();
        $this->employees        = User::allEmployees();
        $this->categories       = TaskCategory::all();
        $this->taskLabels       = TaskLabelList::all();
        $this->taskBoardColumns = TaskboardColumn::all();

        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('id', '!=', $id);

            if ($this->task->project_id != '') {
                $this->allTasks = $this->allTasks->where('project_id', $this->task->project_id);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        $this->upload = can_upload();

        return view('admin.tasks.edit', $this->data);
    }

    public function update(StoreTask $request, $id)
    {
        $task = Task::findOrFail($id);

        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date        = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if ($request->has('without_duedate')) {
            $task->due_date = null;
        }
        else{
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
        $task->task_category_id  = $request->category_id;
        $task->priority          = $request->priority;
        $task->board_column_id   = $request->status;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private        = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable          = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->estimate_hours = $request->estimate_hours;
        $task->estimate_minutes = $request->estimate_minutes;

        $taskBoardColumn = TaskboardColumn::findOrFail($request->status);

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }

        if ($request->project_id != 'all') {
            $task->project_id = $request->project_id;
        } else {
            $task->project_id = null;
        }
        $task->save();

        $task->event_id = $this->googleCalendarEvent($task);
        $task->save();

        // save labels
        $task->labels()->sync($request->task_labels);

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $task->updateCustomFieldData($request->get('custom_fields_data'));
        }


        // Sync task users

        $task->users()->sync($request->user_id);

        $this->calculateProjectProgress($request->project_id);

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskUpdatedSuccessfully'));
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // If it is recurring and allowed by user to delete all its recurring tasks
        if ($request->has('recurring') && $request->recurring == 'yes') {
            Task::where('recurring_task_id', $id)->delete();
        }

        $taskFiles = TaskFile::where('task_id', $id)->get();

        foreach ($taskFiles as $file) {
            Files::deleteFile($file->hashname, 'task-files/' . $file->task_id);
            $file->delete();
        }

        Task::destroy($id);
        //calculate project progress if enabled
        $this->calculateProjectProgress($task->project_id);

        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function create()
    {
        $this->projects   = Project::all();
        $this->employees  = User::allEmployees();
        $this->categories = TaskCategory::all();
        $this->taskLabels = TaskLabelList::all();
        if(request()->id != null){
            $this->taskRequests = TaskRequest::findOrFail(request()->id);
        }
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }

        $this->upload = can_upload();

        $task = new Task();
        $this->fields = $task->getCustomFieldGroupsWithFields()->fields;


        return view('admin.tasks.create', $this->data);
    }

    public function membersList($projectId)
    {
        $this->members = ProjectMember::byProject($projectId);
        $list = view('admin.tasks.members-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function dependentTaskLists($projectId, $taskId = null)
    {
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)
                ->where('project_id', $projectId);

            if ($taskId != null) {
                $this->allTasks = $this->allTasks->where('id', '!=', $taskId);
            }

            $this->allTasks = $this->allTasks->get();
        } else {
            $this->allTasks = [];
        }

        $list = view('admin.tasks.dependent-task-list', $this->data)->render();
        return Reply::dataOnly(['html' => $list]);
    }

    public function store(StoreTask $request)
    {
        $taskIds = [];
        $ganttTaskArray = [];
        $gantTaskLinkArray = [];
        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();
        $task = new Task();
        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if (!$request->has('without_duedate')) {
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
        $task->project_id = $request->project_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $taskBoardColumn->id;
        $task->created_by = $this->user->id;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->estimate_hours = $request->estimate_hours;
        $task->estimate_minutes = $request->estimate_minutes;

        if ($request->board_column_id) {
            $task->board_column_id = $request->board_column_id;
        }

        if ($taskBoardColumn->slug == 'completed') {
            $task->completed_on = Carbon::now()->format('Y-m-d H:i:s');
        } else {
            $task->completed_on = null;
        }
        $task->task_request_id = $request->task_request_id ? $request->task_request_id : null;

        $task->save();
        if($request->task_request_id != null){
            $task_request = TaskRequest::findOrFail($request->task_request_id);
            $task_request->request_status = 'approve';
            $task_request->save();

            
            foreach($task_request->files as $file){
                $oldPath = public_path('user-uploads/task-files-requests/' . $file->task_id .'/'.$file->hashname);
                $newPath = public_path('user-uploads/task-files/'.$task->id.'/'.$file->hashname);
                $path = public_path('user-uploads/task-files/'.$task->id.'/');

                if(file_exists(public_path('user-uploads/task-files/'.$task->id.'/')) == false) {
                    File::makeDirectory($path, 0777, true, true);
                }
                copy($oldPath, $newPath);
                //File::move($oldPath, $newPath);
            }
           
        }
        $taskIds [] = $task->id;
        // save labels
        $task->labels()->sync($request->task_labels);

        // To add custom fields data
        if ($request->get('custom_fields_data')) {
            $task->updateCustomFieldData($request->get('custom_fields_data'));
        }


        // For gantt chart
        if ($request->page_name && $request->page_name == 'ganttChart' && !is_null($task->due_date)) {
            $parentGanttId = $request->parent_gantt_id;
            if(isset($task->due_date)){
                $taskDuration = $task->due_date->diffInDays($task->start_date);
                $taskDuration = $taskDuration + 1;
            }
            $ganttTaskArray[] = [
                'id' => $task->id,
                'text' => $task->heading,
                'start_date' => $task->start_date->format('Y-m-d'),
                'duration' => $taskDuration ?? '',
                'parent' => $parentGanttId,
                'taskid' => $task->id
            ];

            $gantTaskLinkArray[] = [
                'id' => 'link_' . $task->id,
                'source' => $parentGanttId,
                'target' => $task->id,
                'type' => 1
            ];
        }

        if (!$request->has('repeat') || $request->repeat == 'no' && !is_null($task->due_date)) {
            $task->event_id = $this->googleCalendarEvent($task);
            $task->save();
        }

        // Add repeated task
        if ($request->has('repeat') && $request->repeat == 'yes') {
            $repeatCount = $request->repeat_count;
            $repeatType = $request->repeat_type;
            $repeatCycles = $request->repeat_cycles;
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
            if (!$request->has('without_duedate')) {
                $dueDate = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
            }
            for ($i = 1; $i < $repeatCycles; $i++) {
                $repeatStartDate = Carbon::createFromFormat('Y-m-d', $startDate);
                if (!$request->has('without_duedate')) {
                    $repeatDueDate = Carbon::createFromFormat('Y-m-d', $dueDate);
                }
                if ($repeatType == 'day') {
                    $repeatStartDate = $repeatStartDate->addDays($repeatCount);
                    if(isset($repeatDueDate)){
                        $repeatDueDate = $repeatDueDate->addDays($repeatCount);
                    }
                } else if ($repeatType == 'week') {
                    $repeatStartDate = $repeatStartDate->addWeeks($repeatCount);
                    if(isset($repeatDueDate)){
                        $repeatDueDate = $repeatDueDate->addWeeks($repeatCount);
                    }
                } else if ($repeatType == 'month') {
                    $repeatStartDate = $repeatStartDate->addMonths($repeatCount);
                    if(isset($repeatDueDate)){
                        $repeatDueDate = $repeatDueDate->addMonths($repeatCount);
                    }
                } else if ($repeatType == 'year') {
                    $repeatStartDate = $repeatStartDate->addYears($repeatCount);
                    if(isset($repeatDueDate)){
                        $repeatDueDate = $repeatDueDate->addYears($repeatCount);
                    }
                }

                $newTask = new Task();
                $newTask->heading = $request->title;
                if ($request->description != '') {
                    $newTask->description = $request->description;
                }
                $newTask->start_date = $repeatStartDate->format('Y-m-d');
                if(isset($repeatDueDate)){
                    $newTask->due_date = $repeatDueDate->format('Y-m-d');
                }
                $newTask->project_id = $request->project_id;
                $newTask->task_category_id = $request->category_id;
                $newTask->priority = $request->priority;
                $newTask->board_column_id = $taskBoardColumn->id;
                $newTask->created_by = $this->user->id;
                $newTask->recurring_task_id = $task->id;

                $newTask->is_private = $request->has('is_private') && $request->is_private == 'true' ? 1 : 0;
                $newTask->billable = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
                $newTask->estimate_hours = $request->estimate_hours;
                $newTask->estimate_minutes = $request->estimate_minutes;

                if ($request->board_column_id) {
                    $newTask->board_column_id = $request->board_column_id;
                }

                if ($taskBoardColumn->slug == 'completed') {
                    $newTask->completed_on = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    $newTask->completed_on = null;
                }

                $newTask->save();

                $newTask->labels()->sync($request->task_labels);


                // For gantt chart
                if ($request->page_name && $request->page_name == 'ganttChart' && !is_null($newTask->due_date)) {
                    $parentGanttId = $request->parent_gantt_id;
                    $taskDuration = $newTask->due_date->diffInDays($newTask->start_date);
                    $taskDuration = $taskDuration + 1;

                    $ganttTaskArray[] = [
                        'id' => $newTask->id,
                        'text' => $newTask->heading,
                        'start_date' => $newTask->start_date->format('Y-m-d'),
                        'duration' => $taskDuration,
                        'parent' => $parentGanttId,
                        'taskid' => $newTask->id
                    ];

                    $gantTaskLinkArray[] = [
                        'id' => 'link_' . $newTask->id,
                        'source' => $parentGanttId,
                        'target' => $newTask->id,
                        'type' => 1
                    ];
                }
                $startDate = $newTask->start_date->format('Y-m-d');
                if(isset($newTask->due_date)){
                    $dueDate = $newTask->due_date->format('Y-m-d');
                }
                $taskIds [] = $task->id;
            }
            if($task->due_date){
                $this->googleCalendarEventMulti($taskIds);
            }
        }

        //calculate project progress if enabled
        $this->calculateProjectProgress($request->project_id);

        if (!is_null($request->project_id)) {
            $this->logProjectActivity($request->project_id, __('messages.newTaskAddedToTheProject'));
        }

        //log search
        $this->logSearchEntry($task->id, 'Task ' . $task->heading, 'admin.all-tasks.edit', 'task');

        if ($request->page_name && $request->page_name == 'ganttChart') {

            return Reply::successWithData(
                'messages.taskCreatedSuccessfully',
                [
                    'tasks' => $ganttTaskArray,
                    'links' => $gantTaskLinkArray,
                    'taskID' => $task->id,
                ]
            );
        }

        if ($request->board_column_id) {
            return Reply::success(__('messages.taskCreatedSuccessfully'));
        }

        return Reply::dataOnly(['taskID' => $task->id]);
        //        return Reply::redirect(route('admin.all-tasks.index'), __('messages.taskCreatedSuccessfully'));
    }

    public function ajaxCreate(Request $request, $columnId)
    {
        $this->projects = Project::all();
        $this->columnId = $columnId;
        $this->projectId = $request->projectID;
        $this->categories = TaskCategory::all();
        $this->employees = User::allEmployees();
        $completedTaskColumn = TaskboardColumn::where('slug', '!=', 'completed')->first();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', $completedTaskColumn->id)->get();
        } else {
            $this->allTasks = [];
        }
        $this->upload = can_upload();

        return view('admin.tasks.ajax_create', $this->data);
    }

    public function remindForTask($taskID)
    {
        $task = Task::with('users')->findOrFail($taskID);

        // Send  reminder notification to user
        event(new TaskReminderEvent($task));

        return Reply::success('messages.reminderMailSuccess');
    }

    public function show(Request $request, $id)
    {
        $this->task = Task::with('board_column', 'subtasks', 'project', 'files', 'users','taskCommentFiles', 'comments', 'label', 'activeTimerAll')->findOrFail($id)->withCustomFields();
        $this->employees = User::join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('project_time_logs', 'project_time_logs.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id');

        $where = 'and project_time_logs.task_id="' . $id . '" ';
        
        $this->employees = $this->employees->select(
            'users.name',
            'users.image',
            'users.id',
            'designations.name as designation_name',
            DB::raw(
                "(SELECT SUM(project_time_logs.total_minutes) FROM project_time_logs WHERE project_time_logs.user_id = users.id $where GROUP BY project_time_logs.user_id) as total_minutes"
            )
        );
        $this->task_request_file = TaskRequestFile::where('task_id', $this->task->task_request_id )->get();
        $this->employees = $this->employees->where('project_time_logs.task_id', '=', $id);

        $this->employees = $this->employees->groupBy('project_time_logs.user_id')
            ->orderBy('users.name')
            ->get();
        $this->upload = can_upload();
        $this->fields = $this->task->getCustomFieldGroupsWithFields()->fields;
        $this->type = ($request->has('type')) ? $request->type : '';
        $view = view('admin.tasks.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function showFiles($id)
    {
        $this->taskFiles = TaskFile::where('task_id', $id)->get();
        return view('admin.tasks.ajax-file-list', $this->data);
    }

    public function history($id)
    {
        $this->task = Task::with('board_column', 'history', 'history.board_column')->findOrFail($id);
        $view = view('admin.tasks.history', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * @return mixed
     */
    public function pinnedItem()
    {
        $this->pinnedItems = Pinned::join('tasks', 'tasks.id', '=', 'pinned.task_id')
            ->where('pinned.user_id', '=', user()->id)
            ->select('tasks.id', 'heading')
            ->get();

        return view('admin.tasks.pinned-task', $this->data);
    }

    // Google calendar for multiple events
    protected function googleCalendarEventMulti($eventIds)
    {
        if (company() && global_settings()->google_calendar_status == 'active') {
            $google = new Google();
            $company = company();
            $events = Task::whereIn('id', $eventIds)->get();
            $event = $events->first();

            $frq = ['day' => 'DAILY', 'week' => 'WEEKLY', 'month', 'MONTHLY','year' => 'YEARLY'];
            $frequency = $frq[$event->repeat_type];
            $googleAccount = GoogleAccount::where('company_id', company()->id)->first();;

            $eventData = new \Google_Service_Calendar_Event();
            $eventData->setSummary($event->heading);
            $eventData->setLocation('');
            $start = new \Google_Service_Calendar_EventDateTime();
            $start->setDateTime($event->start_date->toAtomString());
            $start->setTimeZone(company()->timezone);
            $eventData->setStart($start);
            $end = new \Google_Service_Calendar_EventDateTime();
            $end->setDateTime($event->due_date->toAtomString());
            $end->setTimeZone(company()->timezone);
            $eventData->setEnd($end);

            $eventData->setRecurrence(array('RRULE:FREQ='.$frequency.';INTERVAL='.$event->repeat_every.';COUNT='.$event->repeat_cycles.';'));

            $attendees = TaskUser::with(['user'])->where('task_id', $event->id)->get();
            $attendiesData = [];
            foreach($attendees as $attend){
                if(!is_null($attend->user) && !is_null($attend->user->email) && !is_null($attend->user->calendar_module) && $attend->user->calendar_module->task_status)
                {
                    $attendee1 = new \Google_Service_Calendar_EventAttendee();
                    $attendee1->setEmail($attend->user->email);
                    $attendiesData[] = $attendee1;
                }

            }
            if ($event->project_id && $event->project_id != '') {
                if ($event->project->client_id != null && $event->project->allow_client_notification == 'enable' && $event->project->client->status != 'deactive') {
                    $attendee2 = new \Google_Service_Calendar_EventAttendee();
                    $attendee2->setEmail($attend->user->email);
                    $attendiesData[] = $attendee2;
                }
            }

            $eventData->attendees = $attendiesData;

            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {
                // Create event
                $google->connectUsing($googleAccount->token);
                // array for multiple

                try {
                    if ($event->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $event->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }
                    foreach($events as $event){
                        $event->event_id = $results->id;
                        $event->save();
                    }
                    return;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            foreach($events as $event){
                $event->event_id = $event->event_id;
                $event->save();
            }
            return;
        }
    }

    // Google calendar for single event
    protected function googleCalendarEvent($event)
    {

        if (company() && global_settings()->google_calendar_status == 'active') {

            $google = new Google();
            $company = company();
            $attendiesData = [];

            $attendees = TaskUser::with(['user'])->where('task_id', $event->id)->get();

            foreach($attendees as $attend){
                if(!is_null($attend->user) && !is_null($attend->user->email) && !is_null($attend->user->calendar_module) && $attend->user->calendar_module->task_status)
                {
                    $attendiesData[] = ['email' => $attend->user->email];
                }
            }

            $googleAccount = GoogleAccount::where('company_id', company()->id)->first();;
            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {

                // Create event
                $google = $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $event->heading,
                    'location' => '',
                    'description' => '',
                    'start' => array(
                        'dateTime' => $event->start_date,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $event->due_date,
                        'timeZone' => $company->timezone,
                    ),
                    'attendees' => $attendiesData,
                    'colorId' => 7,
                    'reminders' => array(
                        'useDefault' => false,
                        'overrides' => array(
                            array('method' => 'email', 'minutes' => 24 * 60),
                            array('method' => 'popup', 'minutes' => 10),
                        ),
                    ),
                ));

                try {
                    if ($event->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $event->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }

                    return $results->id;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            return $event->event_id;
        }
    }

}
