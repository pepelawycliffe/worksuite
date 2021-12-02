@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

<style>
    #attendance-report-table_wrapper .dt-buttons{
        display: none !important;
    }
    #attendance-report-table_filter{
        display: none !important;
    }
</style>
@endpush

@section('content')


    <div class="white-box">
        @section('filter-section')
            <div class="row">
                {!! Form::open(['id'=>'filter-form','class'=>'ajax-form','method'=>'POST']) !!}
                <div class="col-xs-12">
                    <div class="example">
                        <h5 class="box-title">@lang('app.selectDateRange')</h5>
                        <div class="form-group">
                            <div id="reportrange" class="form-control reportrange">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down pull-right"></i>
                            </div>

                            <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                   value="{{ \Carbon\Carbon::today()->startOfMonth()->format($global->date_format) }}"/>
                            <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                   value="{{ \Carbon\Carbon::today()->format($global->date_format) }}"/>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <h5 class="box-title m-t-30">@lang('app.select') @lang('app.employee')</h5>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">
                                <select class="select2 form-control" data-placeholder="@lang('app.all')" id="employeeID" name="employee_id">
                                    <option value="all">@lang('app.all')</option>
                                    @foreach($employees as $employee)
                                        <option
                                                value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-success col-md-6" id="filter-results"><i class="fa fa-check"></i> @lang('app.apply')
                        </button>
                        <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                    </div>

                </div>
                {!! Form::close() !!}

            </div>
        @endsection
    </div>

    <div class="row">

        <div class="col-xs-12">
            <div class="white-box" id="attendanceData">
                <h4 class="dashboard-stats"><span class="text-info" id="totalDays"></span> <span class="font-12 text-muted m-l-5"> @lang('modules.attendance.totalWorkingDays')</span></h4>

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->


@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
@if($global->locale == 'en')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}-AU.min.js"></script>
@elseif($global->locale == 'br')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.pt-BR.min.js"></script>
@else
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.{{ $global->locale }}.min.js"></script>
@endif

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="https://www.mobicollector.com/DataTable/extensions/Buttons/js/dataTables.buttons.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>
{!! $dataTable->scripts() !!}
<script>
    $(function() {
        var dateformat = '{{ $global->moment_format }}';

        var startDate = '{{ \Carbon\Carbon::today()->startOfMonth()->format($global->date_format) }}';
        var start = moment(startDate, dateformat);

        var endDate = '{{ \Carbon\Carbon::today()->format($global->date_format) }}';
        var end = moment(endDate, dateformat);

        function cb(start, end) {
            $('#start-date').val(start.format(dateformat));
            $('#end-date').val(end.format(dateformat));
            $('#reportrange span').html(start.format(dateformat) + ' - ' + end.format(dateformat));
        }
        moment.locale('{{ $global->locale }}');
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,

            locale: {
                language: '{{ $global->locale }}',
                format: '{{ $global->moment_format }}',
            },
            linkedCalendars: false,
            ranges: dateRangePickerCustom
        }, cb);

        cb(start, end);

    });
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#attendance-report-table').on('preXhr.dt', function (e, settings, data) {
        var employeeID = $('#employeeID').val();
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['employee'] = employeeID;
        data['_token'] = '{{ csrf_token() }}';
    });


    var table;
    getTotalData();
    function showTable() {
        getTotalData()
        window.LaravelDataTables["attendance-report-table"].draw();
    }

    function getTotalData(){
        var employeeID = $('#employeeID').val();
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();

        var url2 = '{!!  route('admin.attendance-report.report') !!}';

        $.easyAjax({
            type: 'POST',
            url: url2,
            data: {'employeeID': employeeID, 'startDate': startDate, 'endDate': endDate, '_token': '{{ csrf_token() }}'},
            success: function (response) {
                $('#totalDays').text(response.data);
            }
        });
    }

    $('#filter-results').click(function () {
        showTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('#status').val('all');
        $('.select2').val('all');
        $('#filter-form').find('select').select2();
        $('#start-date').val('{{ \Carbon\Carbon::today()->startOfMonth()->format($global->date_format) }}');
        $('#end-date').val('{{ \Carbon\Carbon::today()->format($global->date_format) }}');
        $('#reportrange span').html('{{ \Carbon\Carbon::today()->startOfMonth()->format($global->date_format) }}' + ' - ' + '{{ \Carbon\Carbon::today()->format($global->date_format) }}');
        $('#filter-results').trigger("click");
    })

    // $(document).ready(function(){
    //     showTable();
    // });

    $('#export-excel').click(function () {
        var employeeID = $('#employeeID').val();
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();


        //refresh datatable
        var url2 = '{!!  route('admin.attendance-report.reportExport', [':startDate', ':endDate', ':employeeID']) !!}';

        url2 = url2.replace(':startDate', startDate);
        url2 = url2.replace(':endDate', endDate);
        url2 = url2.replace(':employeeID', employeeID);

        window.location = url2;
    })


    // showTable();

</script>
@endpush