<?php

namespace App\DataTables\Admin;

use App\DataTables\BaseDataTable;
use App\Task;
use App\TaskRequest;
use App\TaskboardColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class TaskRequestDataTable extends BaseDataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $taskBoardColumns = TaskboardColumn::orderBy('priority', 'asc')->get();

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ($row->request_status == 'approve' || $row->request_status == 'rejected') {
                    $action = '--';
                }else{
                    $action = '<a href="javascript:;" class="btn btn-success approve-task btn-circle"
                    data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="approve task request"><i class="fa fa-check" aria-hidden="true"></i></a>';

                    $action .= '&nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-circle sa-params reject-request"
                    data-toggle="tooltip" data-task-id="' . $row->id . '" data-original-title="Reject request"><i class="fa fa-times" aria-hidden="true"></i></a>';
                }
                return $action;
            })
            ->editColumn('due_date', function ($row) {
                if($row->due_date){
                    if ($row->due_date->endOfDay()->isPast()) {
                        return '<span class="text-danger">' . $row->due_date->format($this->global->date_format) . '</span>';
                    }
                    return '<span class="text-success">' . $row->due_date->format($this->global->date_format) . '</span>';
                }else{
                    return '--';
                }
            })
            ->editColumn('clientName', function ($row) {
                return ($row->clientName) ? ucwords($row->clientName) : '-';
            })
            ->editColumn('created_by', function ($row) {
                if (!is_null($row->created_by)) {
                    return ($row->created_image) ? '<img src="' . asset_url('avatar/' . $row->created_image) . '"
                                                            alt="user" class="img-circle" width="30" height="30"> ' . ucwords($row->created_by) : '<img src="' . asset('img/default-profile-3.png') . '"
                                                            alt="user" class="img-circle" width="30" height="30"> ' . ucwords($row->created_by);
                }
                return '-';
            })
            ->editColumn('heading', function ($row) {
                $pin = '';
                if(($row->pinned_task) ){
                    $pin = '<br><span class="font-12"  data-toggle="tooltip" data-original-title="'.__('app.pinned').'"><i class="icon-pin icon-2"></i></span>';
                }

                $name = '<a href="javascript:;" data-task-id="' . $row->id . '" class="show-task-detail">' . ucfirst($row->heading) . '</a> '.$pin;

                if ($row->is_private) {
                    $name .= ' <i data-toggle="tooltip" data-original-title="' . __('app.private') . '" class="fa fa-lock" style="color: #ea4c89"></i>';
                }

                if (count($row->activeTimerAll) > 0) {
                    
                    $name .= ' <i data-toggle="tooltip" data-original-title="' . __('modules.projects.activeTimers') . '" class="fa fa-clock-o" style="color: #679c0d"></i>';
                }
                return $name;
            })
            ->editColumn('board_column', function ($row) {
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
           
            ->editColumn('project_name', function ($row) {
                if (is_null($row->project_id)) {
                    return '';
                }
                return '<a href="' . route('admin.projects.show', $row->project_id) . '">' . ucfirst($row->project_name) . '</a>';
            })
            ->rawColumns(['board_column', 'action', 'project_name', 'clientName', 'due_date', 'created_by', 'heading'])
            ->removeColumn('project_id')
            ->removeColumn('image')
            ->removeColumn('created_image')
            ->removeColumn('label_color');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TaskRequest $model)
    {
        $request = $this->request();
        $startDate = null;
        $endDate = null;

        $projectId = $request->projectId;
        $hideCompleted = $request->hideCompleted;
        $taskBoardColumn = TaskboardColumn::where('slug', 'completed')->first();

        $model = $model->leftJoin('projects', 'projects.id', '=', 'task_requests.project_id')
            ->leftJoin('users as client', 'client.id', '=', 'projects.client_id')
            ->leftJoin('users as creator_user', 'creator_user.id', '=', 'task_requests.created_by')
            ->leftJoin('task_labels', 'task_labels.task_id', '=', 'task_requests.id')
            ->leftJoin('task_category', 'task_category.id', '=', 'task_requests.task_category_id')
            ->selectRaw('task_requests.id, projects.project_name, task_requests.heading, client.name as clientName,  creator_user.image as created_image,task_requests.request_status,
             task_requests.due_date,
              task_requests.project_id ,( select count("id") from pinned where pinned.task_id = task_requests.id and pinned.user_id = '.user()->id.') as pinned_task')
            ->with('activeTimerAll')
            ->groupBy('task_requests.id');

        if (($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') && $request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(task_requests.`due_date`)'), [$startDate, $endDate]);
                $q->orWhereBetween(DB::raw('DATE(task_requests.`start_date`)'), [$startDate, $endDate]);
            });
        }
        if ($projectId != 0 && $projectId != null && $projectId != 'all') {
            $model->where('task_requests.project_id', '=', $projectId);
        }
        if ($request->clientID != '' && $request->clientID != null && $request->clientID != 'all') {
            $model->where('projects.client_id', '=', $request->clientID);
        }
        if ($request->billable != '' && $request->billable != null && $request->billable != 'all') {
            $model->where('task_requests.billable', '=', $request->billable);
        }
        if ($request->task_category != '' && $request->task_category != null && $request->task_category != 'all') {
            $model->where('task_requests.task_category_id', '=', $request->task_category);
        }
        if ($hideCompleted == '1') {
            $model->where('task_requests.board_column_id', '<>', $taskBoardColumn->id);
        }
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('allTasks-table')
            ->columns($this->processTitle($this->getColumns()))
            ->minifiedAjax()
            ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            ->stateSave(true)
            ->processing(true)
            ->language(__('app.datatable'))
            ->buttons(
                Button::make(['extend' => 'export', 'buttons' => ['excel', 'csv'], 'text' => '<i class="fa fa-download"></i> ' . trans('app.exportExcel') . '&nbsp;<span class="caret"></span>'])
            )
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["allTasks-table"].buttons().container()
                    .appendTo( ".bg-title .text-right")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $modules = $this->user->modules;

        $dsData = [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'exportable' => false],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false],
            __('app.task') => ['data' => 'heading', 'name' => 'heading'],

            // __('modules.tasks.assigned') => ['data' => 'name', 'name' => 'name', 'visible' => false],
            // __('modules.tasks.assignTo') => ['data' => 'users', 'name' => 'member.name', 'exportable' => false],
            __('app.dueDate') => ['data' => 'due_date', 'name' => 'due_date'],
            // __('app.status') => ['data' => 'status', 'name' => 'status', 'visible' => false],
            __('app.columnStatus') => ['data' => 'board_column', 'name' => 'board_column', 'exportable' => false, 'searchable' => false],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-center')
        ];

        if(in_array('projects', $modules) ){
            $dsData = array_slice($dsData, 0, 3, true) + [__('app.project')  => ['data' => 'project_name', 'name' => 'projects.project_name']] + array_slice($dsData, 3, count($dsData) - 1, true);
        }

        return $dsData;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'All_Task_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);
        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }

}
