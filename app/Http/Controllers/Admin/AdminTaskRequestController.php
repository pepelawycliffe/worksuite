<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\TaskRequestDataTable;
use App\TaskRequest;
use App\TaskRequestFile;
use App\Helper\Files;
use App\Helper\Reply;
use App\Project;
use App\ProjectMilestone;
use App\Task;
use App\TaskCategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminTaskRequestController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.taskList';
        $this->pageIcon = 'ti-layout-list-thumb';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('tasks', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index(TaskRequestDataTable $dataTable)
    {
        $this->projects = Project::all();
        $this->milestones = ProjectMilestone::all();
        $this->clients = User::allClients();
        $this->employees = User::allEmployees();
        $this->taskCategories = TaskCategory::all();
        $this->taskRequests = TaskRequest::all();
        $this->startDate = Carbon::today()->subDays(15)->format($this->global->date_format);
        $this->endDate = Carbon::today()->addDays(15)->format($this->global->date_format);

        return $dataTable->render('admin.task-request.index', $this->data);
    }
    
    //reject task request
    public function rejectTask(Request $request ,$id )
    {
        $taskRequests = TaskRequest::findOrFail($id);
        $taskRequests->request_status = 'rejected';
        $taskRequests->save();
        return Reply::success(__('messages.taskRejectedSuccessfully'));
    }
   
    public function show($id)
    {
        $this->task = TaskRequest::with('board_column', 'subtasks', 'project', 'files', 'taskCommentFiles', 'comments', 'label', 'activeTimerAll')->findOrFail($id)->withCustomFields();
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

        $this->employees = $this->employees->where('project_time_logs.task_id', '=', $id);

        $this->employees = $this->employees->groupBy('project_time_logs.user_id')
            ->orderBy('users.name')
            ->get();
        $this->upload = can_upload();
        $view = view('admin.task-request.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function deleteTaskFile(Request $request, $id)
    {
        $file = TaskRequestFile::findOrFail($id);
        Files::deleteFile($file->hashname, 'task-files/'.$file->task_id);
        TaskRequestFile::destroy($id);
        $this->taskFiles = TaskRequestFile::where('task_id', $file->task_id)->get();
        $view = view('admin.tasks.ajax-list', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['html' => $view, 'totalFiles' => sizeof($this->taskFiles)]);
    }

    public function download($id)
    {
        $file = TaskRequestFile::findOrFail($id);
        return download_local_s3($file, 'task-files-requests/' . $file->task_id.'/'.$file->hashname);
    }
    
}
