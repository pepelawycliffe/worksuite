@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right bg-title-right">

            <a href="{{ route('admin.all-tasks.index') }}" class="btn btn-outline btn-primary btn-sm"> @lang('app.taskList') </a>
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
    .swal-footer {
        text-align: center !important;
    }
    .filter-section::-webkit-scrollbar {
    display: block !important;
}
</style>
@endpush


@section('filter-section')
    <div class="row m-b-10">
        {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="col-xs-12">
            <div class="example">
                <h5 class="box-title">@lang('app.selectDateRange')</h5>
                <div id="reportrange" class="form-control reportrange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down pull-right"></i>
                </div>

                <input type="hidden" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                       value=""/>
                <input type="hidden" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                       value=""/>
            </div>
        </div>

            @if(in_array('projects', $modules))
                <div class="col-xs-12">
                    <h5 class="box-title">@lang('app.selectProject')</h5>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">
                                <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                                    <option value="all">@lang('app.all')</option>
                                    @foreach($projects as $project)
                                        <option
                                                value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-xs-12">
                <h5 class="box-title">@lang('app.select') @lang('app.client')</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <select class="select2 form-control" data-placeholder="@lang('app.client')" id="clientID">
                                <option value="all">@lang('app.all')</option>
                                @foreach($clients as $client)
                                    <option
                                            value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <h5 class="box-title">@lang('app.billableTask')</h5>
                    <select class="form-control select2" name="billable" id="billable" data-style="form-control">
                        <option value="all">@lang('modules.client.all')</option>
                        <option value="1">@lang('app.yes')</option>
                        <option value="0">@lang('app.no')</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <h5 class="box-title">@lang('app.category')</h5>
                    <select class="form-control select2" name="task_category" id="task_category" data-style="form-control">
                        <option value="all">@lang('modules.client.all')</option>
                        @foreach($taskCategories as $category)
                        <option
                            value="{{ $category->id }}">{{ $category->category_name}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

            <div class="col-xs-12">
                </button>
                <div class="form-group">
                    <label class="control-label col-xs-12">&nbsp;</label>
                    <button type="button" id="filter-results" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    @endsection

    @section('content')

        <div class="row">
            <div class="col-xs-12">
                <div class="white-box">

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

    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script>
        $(function() {
            var dateformat = '{{ $global->moment_format }}';

            var start = '';
            var end = '';

            function cb(start, end) {
                if(start){
                    $('#start-date').val(start.format(dateformat));
                    $('#end-date').val(end.format(dateformat));
                    $('#reportrange span').html(start.format(dateformat) + ' - ' + end.format(dateformat));
                }

            }
            moment.locale('{{ $global->locale }}');
            $('#reportrange').daterangepicker({
                // startDate: start,
                // endDate: end,
                locale: {
                    language: '{{ $global->locale }}',
                    format: '{{ $global->moment_format }}',
                },
                linkedCalendars: false,
            ranges: dateRangePickerCustom
            }, cb);

            cb(start, end);

        });
        $(document).ready(function(){
            showTable();
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#allTasks-table').on('preXhr.dt', function (e, settings, data) {
            var startDate = $('#start-date').val();

            if (startDate == '') {
                startDate = null;
            }

            var endDate = $('#end-date').val();

            if (endDate == '') {
                endDate = null;
            }

            var projectID = $('#project_id').val();
            if (!projectID) {
                projectID = 0;
            }
            var clientID = $('#clientID').val();
            var assignedBY = $('#assignedBY').val();
            var assignedTo = $('#assignedTo').val();
            var status = $('#status').val();
            var category_id = $('#category_id').val();
            var label = $('#label').val();
             var billable = $('#billable').val();
             var milestone = $('#milestone').val();
             var task_category = $('#task_category').val();


            if ($('#hide-completed-tasks').is(':checked')) {
                var hideCompleted = '1';
            } else {
                var hideCompleted = '0';
            }

            data['clientID'] = clientID;
            data['assignedBY'] = assignedBY;
            data['assignedTo'] = assignedTo;
            data['status'] = status;
            data['category_id'] = category_id;
            data['hideCompleted'] = hideCompleted;
            data['projectId'] = projectID;
            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['label'] = label;
            data['billable'] = billable;
            data['milestone'] = milestone;
            data['task_category'] = task_category;


        });

        table = '';

        function showTable() {
            window.LaravelDataTables["allTasks-table"].draw();
        }

        $('#filter-results').click(function () {
            showTable();
        });

        $('#reset-filters').click(function () {
            $('.select2').val('all');
            $('.select2').trigger('change');
            $(".selectpicker").val('all');
            $(".selectpicker").selectpicker("refresh");
            $('#start-date').val('');
            $('#end-date').val('');
            $('#reportrange span').html('');
            showTable();
        })


        $('body').on('click', '.approve-task', function () {
            var id = $(this).data('task-id');
            var buttons = {
                cancel: "@lang('app.no')",
                confirm: {
                    text: "@lang('app.yes')",
                    value: 'confirm',
                    visible: true,
                    className: "danger",
                }
            };
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.approveMessage')",
                dangerMode: true,
                icon: 'warning',
                buttons: buttons
            }).then(function (isConfirm) {
                if (isConfirm == 'confirm') {
                    var url = '{{ route('admin.all-tasks.create')}}?id='+id;
                    window.location.href = url;

                    // location.href('url');
                }
            });
        });
        $('body').on('click', '.sa-params', function () {
            var id = $(this).data('task-id');

            var buttons = {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.confirmation.rejectConfirm')",
                    value: 'confirm',
                    visible: true,
                    className: "danger",
                }
            };
            swal({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.confirmation.rejectMessage')",
                dangerMode: true,
                icon: 'warning',
                buttons: buttons
            }).then(function (isConfirm) {
                if (isConfirm == 'confirm') {

                    var url = "{{ route('admin.task-request.reject-tasks',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    var dataObject = {'_token': token, '_method': 'POST'};
                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: dataObject,
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                window.LaravelDataTables["allTasks-table"].draw();
                            }
                        }
                    });
                }
            });
        });
        $('#allTasks-table').on('click', '.show-task-detail', function () {
            $(".right-sidebar").slideDown(50).addClass("shw-rside");

            var id = $(this).data('task-id');
            var url = "{{ route('admin.task-request.show',':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status == "success") {
                        $('#right-sidebar-content').html(response.view);
                    }
                }
            });
        })

    </script>
    @endpush
