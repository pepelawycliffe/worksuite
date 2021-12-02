<?php

namespace App\Http\Controllers\Client;

use App\Helper\Reply;
use App\ModuleSetting;
use App\Project;
use App\SubTask;
use App\TaskCategory;
use App\TaskRequest;
use App\User;
use App\Http\Requests\Tasks\StoreClientTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ClientTaskRequestController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.projects';
        $this->pageIcon = 'icon-layers';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('tasks', $this->user->modules), 403);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.task-request.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientTask $request)
    {
        $task = new TaskRequest();
        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if (!$request->has('without_duedate')) {
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
        $task->project_id = $request->project_id;
        $task->priority = $request->priority;
        $task->task_category_id  = $request->category_id;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->billable   = $request->has('billable') && $request->billable == 'true' ? 1 : 0;
        $task->created_by = $this->user->id;
        $task->request_status = 'pending';
        $task->save();

        $this->project = Project::findOrFail($task->project_id);
        //        $view = view('client.projects.tasks.task-list-ajax', $this->data)->render();

        return Reply::dataOnly(['taskID' => $task->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->upload = can_upload();

        $this->tasks = TaskRequest::leftJoin('projects', 'projects.id', '=', 'task_requests.project_id')
            ->where('task_requests.project_id', '=', $id)
            ->where('projects.client_id', '=', $this->user->id)
            ->select('task_requests.*')
            ->get();
        $this->project = Project::findOrFail($id);
        $this->employees  = User::withoutGlobalScope('active')->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.email_notifications', 'users.created_at', 'users.image')
            ->where('roles.name', '<>', 'client')
            ->where('users.company_id', company()->id)
            ->orderBy('users.name', 'asc')
            ->groupBy('users.id')
            ->get();
        $this->categories = TaskCategory::all();

        if ($this->project->client_view_task == 'disable') {
            abort(403);
        }
        return view('client.task-request.index', $this->data);
    }

    public function showTask($id)
    {
        $this->task = TaskRequest::findOrFail($id);
        $this->upload = can_upload();
        $view = view('client.task-request.show', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->task = TaskRequest::findOrFail($id);
        $this->upload = can_upload();

        if ($this->task->project->client_view_task == 'disable' || $this->task->created_by != $this->user->id) {
            abort(403);
        }

        $this->categories = TaskCategory::all();
            $this->allTasks = TaskRequest::where('id', '!=', $id);

        if ($this->task->project_id != '') {
            $this->allTasks = $this->allTasks->where('project_id', $this->task->project_id);
        }

            $this->allTasks = $this->allTasks->get();
      
        $view = view('client.task-request.edit', $this->data)->render();
        return Reply::dataOnly(['html' => $view]);
    }

    public function checkTask($taskID)
    {
        $task = TaskRequest::findOrFail($taskID);
        $subTask = SubTask::where(['task_id' => $taskID, 'status' => 'incomplete'])->count();
        return Reply::dataOnly(['taskCount' => $subTask, ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreClientTask $request, $id)
    {
        $task = TaskRequest::findOrFail($id);
        if ($task->created_by != $this->user->id) {
            abort(403);
        }
        $task->heading = $request->title;
        if ($request->description != '') {
            $task->description = $request->description;
        }
        $task->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d');
        if (!$request->has('without_duedate')) {
            $task->due_date = Carbon::createFromFormat($this->global->date_format, $request->due_date)->format('Y-m-d');
        }
            $task->priority = $request->priority;
        $task->dependent_task_id = $request->has('dependent') && $request->dependent == 'yes' && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->save();
        return Reply::dataOnly(['taskID' => $task->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = TaskRequest::findOrFail($id);

        if ($task->created_by != $this->user->id) {
            abort(403);
        }
        // Delete current task
        TaskRequest::destroy($id);
        return Reply::success(__('messages.taskDeletedSuccessfully'));
    }

    public function data(Request $request, $projectId = null)
    {
        
        $task_requests = TaskRequest::leftJoin('projects', 'projects.id', '=', 'task_requests.project_id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'task_requests.created_by')
            ->leftJoin('users as client', 'client.id', '=', 'projects.client_id')
            ->where('task_requests.project_id', '=', $projectId)
            ->where('projects.client_id', '=', $this->user->id)
            ->select('task_requests.id', 'task_requests.request_status', 'projects.project_name', 'projects.read_only', 'task_requests.heading', 'client.name as clientName', 'creator_user.name as created_by', 'creator_user.image as created_image', 'task_requests.due_date', 'task_requests.project_id', 'task_requests.created_by as created_id')
            ->groupBy('task_requests.id');

        $task_requests->get();
        return DataTables::of($task_requests)
            ->addColumn('action', function ($row) {
                if($row->read_only == 'enable'){
                    if ($row->created_id == $this->user->id && $row->request_status != 'approve' ) {
                        return '<a href="javascript:;" class="btn btn-info btn-circle edit-task"
                        data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            &nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                        data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                        
                    } else {
                        return '--';
                    }
                }else{
                    return '--';
                }
               
            })
            ->editColumn('due_date', function ($row) {
                if($row->due_date){
                    if ($row->due_date->isPast()) {
                        return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                    }
                    return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
                }else{
                    return '--';
                }
            })
            ->editColumn('created_by', function ($row) {
                if (!is_null($row->created_by) && $row->created_id == $this->user->id) {
                    return 'You';
                } elseif (!is_null($row->created_by) && $row->created_id != $this->user->id) {
                    return ($row->created_image) ? '<img src="' . asset_url('avatar/' . $row->created_image) . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->created_by) : '<img src="' . asset('img/default-profile-3.png') . '"
                                                            alt="user" class="img-circle" width="25" height="25"> ' . ucwords($row->created_by);
                }
                return '-';
            })
            ->editColumn('heading', function ($row) {
                return '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail">' . ucfirst($row->heading) . '</a>';
            })
            ->editColumn('column_name', function ($row) {
                return '<label class="label" style="background-color: ' . $row->label_color . '">' . $row->column_name . '</label>';
            })
            ->editColumn('request_status', function ($row) {
                $others = '<ul style="list-style: none; padding: 0; ">';

                if ($row->request_status == 'pending') {
                    $others .= '<li>'.' <label class="label label-warning">' . ucfirst($row->request_status) . '</label></li>';
                } elseif ($row->request_status == 'approve') {
                    $others .= '<li>'.' <label class="label label-success">' . ucfirst($row->request_status) . '</label></li>';
                } elseif ($row->request_status == 'rejected') {
                    $others .= '<li>'.' <label class="label label-danger">' . ucfirst ($row->request_status) . '</label></li>';
                }
                return ucfirst($others);
            })
            ->rawColumns(['column_name', 'request_status','action', 'clientName', 'due_date', 'users', 'created_by', 'heading'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->removeColumn('created_image')
            ->removeColumn('label_color')
            ->addIndexColumn()
            ->make(true);
    }

}
