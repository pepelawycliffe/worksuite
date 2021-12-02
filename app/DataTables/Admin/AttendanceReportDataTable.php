<?php

namespace App\DataTables\Admin;

use App\Attendance;
use App\AttendanceSetting;
use App\DataTables\BaseDataTable;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;

class AttendanceReportDataTable extends BaseDataTable
{

    public function __construct()
    {
        parent::__construct();
        $this->attendanceSettings = AttendanceSetting::first();
    }
    /**
     * @param $query
     * @return \Yajra\DataTables\CollectionDataTable|\Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $request = $this->request();

        $startDate = $startDate = now($this->global->timezone)->startOfMonth();
        $endDate = $endDate = now($this->global->timezone);

        if ($request->startDate != '') {
            $startDate = $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate);
            $endDate = $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate);
        }

        $openDays = json_decode($this->attendanceSettings->office_open_days);

        $this->totalWorkingDays = $startDate->diffInDaysFiltered(function (Carbon $date) use ($openDays) {
            foreach ($openDays as $day) {
                if ($date->dayOfWeek == $day) {
                    return $date;
                }
            }
        }, $endDate);
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('present_days', function ($row) {
                return $row->presentCount;
            })
            ->addColumn('absent_days', function ($row) {
                return (($this->totalWorkingDays - $row->presentCount) < 0) ? '0' : ($this->totalWorkingDays - $row->presentCount);
            })
            ->addColumn('hours_clocked', function ($row) {
                if($row->totalHours > 0 )
                {
                    $timeLog = intdiv($row->totalHours, 60) . ' ' . __('app.hrs') . ' ';

                    if (($row->totalHours % 60) > 0) {
                        $timeLog .= ($row->totalHours % 60) . ' ' . __('app.mins');
                    }
                    return $timeLog;
                }
                return 0;
            })
            ->addColumn('late_day_count', function ($row) {
                return $row->lateCount;
            })
            ->addColumn('half_day_count', function ($row){
                return $row->halfDay;
            });

    }

    /**
     * @param User $model
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        $request = $this->request();
        $startDate = $startDate = now($this->global->timezone)->startOfMonth();
        $endDate = $endDate = now($this->global->timezone);

        if ($request->startDate != '') {
            $startDate = $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate);
            $endDate = $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate);
        }

        $newStartDate = $startDate->format('Y-m-d');
        $newEndDate = $endDate->format('Y-m-d');

        $model = User::with('role', 'roles', 'employeeDetail')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('attendances', 'attendances.user_id', '=', 'users.id')
            ->where('roles.name', 'employee')
            ->selectRaw('users.*, (SELECT count(DISTINCT DATE(attends.clock_in_time) ) as lateCount from attendances as attends where DATE(attends.clock_in_time) >= "' . $newStartDate . '" and DATE(attends.clock_in_time) <= "' . $newEndDate . '" and user_id=users.id and attends.late = "yes" and attends.company_id = '.company()->id.') as lateCount,
            (SELECT count(DISTINCT DATE(attendlate.clock_in_time) ) as presentCount from attendances as attendlate where DATE(attendlate.clock_in_time) >= "' . $newStartDate . '" and DATE(attendlate.clock_in_time) <= "' . $newEndDate . '" and user_id=users.id and attendlate.company_id = '.company()->id.') as presentCount,
            (SELECT count(DISTINCT DATE(attendhalf.clock_in_time) ) as presentCount from attendances as attendhalf where DATE(attendhalf.clock_in_time) >= "' . $newStartDate . '" and DATE(attendhalf.clock_in_time) <= "' . $newEndDate . '" and user_id=users.id and attendhalf.half_day = "yes" and attendhalf.company_id = '.company()->id.') as halfDay,
            (SELECT  SUM(TIMESTAMPDIFF(MINUTE,attendhour.clock_in_time, IFNULL(attendhour.clock_out_time, CONCAT(DATE(attendhour.clock_in_time)," ", "'.$this->attendanceSettings->office_end_time.'")))) as totalmin from attendances as attendhour where DATE(attendhour.clock_in_time) >= "' . $newStartDate . '" and DATE(attendhour.clock_in_time) <= "' . $newEndDate . '" and attendhour.user_id=users.id and attendhour.company_id = '.company()->id.' limit 1) as totalHours');

        if ($request->employee != 'all') {
            $model = $model->where('users.id', $request->employee);
        }

        return $model->groupBy('users.id');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('attendance-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            /* ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>") */
            ->orderBy(0)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            /* ->stateSave(true) */
            ->processing(true)
            ->language(__('app.datatable'))
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["attendance-report-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]))
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["attendance-report-table"].buttons().container()
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
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false],
            __('app.employee')  => ['data' => 'name', 'name' => 'users.name'],
            __('modules.attendance.present') => ['data' => 'present_days', 'name' => 'present_days'],
            __('modules.attendance.absent') => ['data' => 'absent_days', 'name' => 'absent_days'],
            __('modules.attendance.hoursClocked') => ['data' => 'hours_clocked', 'name' => 'hours_clocked'],
            __('app.days') . ' ' . __('modules.attendance.late') => ['data' => 'late_day_count', 'name' => 'late_day_count'],
            __('modules.attendance.halfDay') => ['data' => 'half_day_count', 'name' => 'half_day_count'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Attendance_report_' . date('YmdHis');
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
