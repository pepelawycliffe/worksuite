@extends('layouts.client-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 bg-title-left">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }} #{{ $project->id }} - <span
                        class="font-bold">{{ ucwords($project->project_name) }}</span></h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 bg-title-right">
            <ol class="breadcrumb">
                <li><a href="{{ route('client.dashboard.index') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('client.projects.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.invoices')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<style>
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush
@section('content')

    <div class="row">
        <div class="col-xs-12">

            <section>
                <div class="sttabs tabs-style-line">
                        @include('client.projects.show_project_menu')
                    <div class="sttabs tabs-style-line">
                        <div class="white-box">
                            <nav>
                                <ul class="showtaskTabs">
                                        <li class= "all_task" ><a href="{{ route('client.tasks.edit', $project->id) }}"><span>@lang('app.task')</span></a></li>
                                    <li class= "task_request" ><a href="{{ route('client.tasks-request.show', $project->id) }}"><span>@lang('modules.tasks.requestTask')</span></a></li>
    
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-xs-12" id="task-list-panel">
                                    <div class="white-box">
                                        <div class="row m-b-10">
                                            <div class="col-md-12 hide" id="new-task-panel">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading "><i class="ti-plus"></i> @lang('modules.tasks.requestTask')
                                                        <div class="panel-action">
                                                            <a href="javascript:;" id="hide-new-task-panel"><i class="ti-close"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="panel-wrapper collapse in">
                                                        <div class="panel-body">
                                                            {!! Form::open(['id'=>'createTask','class'=>'ajax-form','method'=>'POST']) !!}

                                                            {!! Form::hidden('project_id', $project->id) !!}

                                                            <div class="form-body">
                                                                <div class="row">
                                                                    <div class="col-xs-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label required">@lang('app.heading')</label>
                                                                            <input type="text" id="heading" name="title"
                                                                                   class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <!--/span-->
                                                                    <div class="col-xs-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label">@lang('app.description')</label>
                                                                            <textarea id="description" name="description"
                                                                                      class="form-control summernote"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="form-group">

                                                                            <div class="checkbox checkbox-info">
                                                                                <input id="billable-task" name="billable" value="true"
                                                                                       type="checkbox">
                                                                                <label for="billable-task">@lang('modules.tasks.billable') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.billableInfo')</span></span></span></a></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-xs-12">
                                                                        <div class="form-group">

                                                                            <div class="checkbox checkbox-info">
                                                                                <input id="dependent-task" name="dependent" value="yes"
                                                                                       type="checkbox">
                                                                                <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" id="dependent-fields" style="display: none">
                                                                        <div class="col-xs-12">
                                                                            <div class="form-group">
                                                                                <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                                                                <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseTask')" name="dependent_task_id" id="dependent_task_id" >
                                                                                    <option value="">--</option>
                                                                                    @foreach($tasks as $allTask)
                                                                                        <option value="{{ $allTask->id }}">{{ $allTask->heading }} (@lang('app.dueDate'): {{ ($allTask->due_date) ? $allTask->due_date->format($global->date_format): '' }})</option>
                                                                                    @endforeach

                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label class="control-label required">@lang('modules.projects.startDate')</label>
                                                                            <input type="text" name="start_date" id="start_date" class="form-control" autocomplete="off" value="">
                                                                        </div>
                                                                    </div>
                                                                    <!--/span-->
                                                                    <div class="col-md-4" id="duedateBox">
                                                                        <div class="form-group">
                                                                            <label class="control-label required">@lang('app.dueDate')</label>
                                                                            <input type="text" name="due_date" id="due_date"
                                                                                   autocomplete="off" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group" style="padding-top: 20px;">
                                                                            <div class="checkbox checkbox-info">
                                                                                <input id="without_duedate" name="without_duedate" value="true"
                                                                                       type="checkbox">
                                                                                <label for="without_duedate">@lang('modules.tasks.withoutDuedate')</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!--/span-->

                                                                    <div class="col-xs-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label">@lang('modules.tasks.taskCategory')
                                                                            </label>
                                                                            <select class="selectpicker form-control" name="category_id" id="category_id"
                                                                                    data-style="form-control">
                                                                                @forelse($categories as $category)
                                                                                    <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                                                                @empty
                                                                                    <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                                                                @endforelse
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <!--/span-->
                                                                    <div class="col-xs-12">
                                                                        <div class="form-group">
                                                                            <label class="control-label">@lang('modules.tasks.priority')</label>

                                                                            <div class="radio radio-danger">
                                                                                <input type="radio" name="priority" id="radio13"
                                                                                       value="high">
                                                                                <label for="radio13" class="text-danger">
                                                                                    @lang('modules.tasks.high') </label>
                                                                            </div>
                                                                            <div class="radio radio-warning">
                                                                                <input type="radio" name="priority" checked
                                                                                       id="radio14" value="medium">
                                                                                <label for="radio14" class="text-warning">
                                                                                    @lang('modules.tasks.medium') </label>
                                                                            </div>
                                                                            <div class="radio radio-success">
                                                                                <input type="radio" name="priority" id="radio15"
                                                                                       value="low">
                                                                                <label for="radio15" class="text-success">
                                                                                    @lang('modules.tasks.low') </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row m-b-20">
                                                                        <div class="col-xs-12">
                                                                            {{-- @if($upload) --}}
                                                                                <button type="button"
                                                                                        class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button"
                                                                                        style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i>
                                                                                    File Select Or Upload
                                                                                </button>
                                                                                <div id="file-upload-box">
                                                                                    <div class="row" id="file-dropzone">
                                                                                        <div class="col-xs-12">
                                                                                            <div class="dropzone"
                                                                                                 id="file-upload-dropzone">
                                                                                                {{ csrf_field() }}
                                                                                                <div class="fallback">
                                                                                                    <input name="file" type="file" multiple/>
                                                                                                </div>
                                                                                                <input name="image_url" id="image_url" type="hidden"/>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <input type="hidden" name="taskID" id="taskID">
                                                                            {{-- @else
                                                                                <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                                                            @endif --}}
                                                                        </div>
                                                                    </div>
                                                                    <!--/span-->
                                                                </div>
                                                                <!--/row-->

                                                            </div>
                                                            <div class="form-actions">
                                                                <button type="submit" id="save-task" class="btn btn-success"><i
                                                                            class="fa fa-check"></i> @lang('app.save')
                                                                </button>
                                                            </div>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 hide" id="edit-task-panel">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="white-box">
                                            <div class="row m-b-10">
                                                <div class="col-md-6">
                                                    <a href="javascript:;" id="show-new-task-panel" class="btn btn-success btn-outline btn-sm">
                                                        <i class="fa fa-plus"></i>
                                                        @lang('modules.tasks.requestTaskAdd')
                                                    </a>

                                                </div>

                                            </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable"
                                                   id="tasks-table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>@lang('app.task')</th>
                                                    <th>@lang('app.client')</th>
                                                    <th>@lang('modules.tasks.assignBy')</th>
                                                    <th>@lang('app.dueDate')</th>
                                                    <th>@lang('app.requestStatus')</th>
                                                    <th>@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}


@endsection

@push('footer-script')
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>


<script type="text/javascript">
    var newTaskpanel = $('#new-task-panel');
    var taskListPanel = $('#task-list-panel');
    var editTaskPanel = $('#edit-task-panel');

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });
    $('#without_duedate').click(function () {
            var check = $('#without_duedate').is(":checked") ? true : false;
            if(check == true){
                $('#duedateBox').hide();
                $('#due_date2').val('');
            }
            else{
            $('#duedateBox').show();
            }
        });
        
    function showTable() {
        var url = '{!!  route('client.tasks-request.data', [':projectId']) !!}?_token={{ csrf_token() }}';

        url = url.replace(':projectId', '{{ $project->id }}');

        $('#tasks-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                "url": url,
                "type": "POST"
            },
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function (oSettings) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "order": [[0, "desc"]],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'heading', name: 'heading'},
                {data: 'clientName', name: 'client.name', bSort: false},
                {data: 'created_by', name: 'creator_user.name'},
                {data: 'due_date', name: 'due_date'},
                {data: 'request_status', name: 'task_requests.request_status'},
                {data: 'action', name: 'action', "searchable": false}
            ]
        });
    }

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('task-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.recoverDeletedTask')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('client.tasks-request.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            showTable();
                        }
                    }
                });
            }
        });
    });
    $('#tasks-table').on('click', '.show-task-detail', function () {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");

        var id = $(this).data('task-id');
        var url = "{{ route('client.tasks-request.show-task',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                    $("#right-sidebar-content").css({"height": "100%","overflow-y": "auto"});
                }
            }
        });
    })

    jQuery('#due_date, #start_date').datepicker({
        format: '{{ $global->date_picker_format }}',
        autoclose: true,
        todayHighlight: true
    });

    showTable();

    //    save new task
    $('#save-task').click(function () {
        $.easyAjax({
            url: '{{route('client.tasks-request.store')}}',
            container: '#section-line-3',
            type: "POST",
            data: $('#createTask').serialize(),
            success: function (data) {
                $('#createTask').trigger("reset");
                $('.summernote').summernote('code', '');
                var dropzone = 0;
                    @if($upload)
                        dropzone = myDropzone.getQueuedFiles().length;
                    @endif
                    if(dropzone > 0){
                        taskID = data.taskID;
                        console.log(taskID);
                        $('#taskID').val(data.taskID);
                        myDropzone.processQueue();
                    } else {
                        var msgs = "@lang('messages.taskCreatedSuccessfully')";
                        $.showToastr(msgs, 'success');
                        showTable();
                    }
                newTaskpanel.switchClass("show", "hide", 300, "easeInOutQuad");
                // showTable();
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
                location.reload();

            }
        })
    });

    //    save new task
    taskListPanel.on('click', '.edit-task', function () {
        var id = $(this).data('task-id');
        var url = "{{route('client.tasks-request.edit', ':id')}}";
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            container: '#task-list-panel',
            data: {taskId: id},
            success: function (data) {
                editTaskPanel.html(data.html);
                // taskListPanel.switchClass("col-md-12", "col-md-6", 1000, "easeInOutQuad");
                newTaskpanel.addClass('hide').removeClass('show');
                editTaskPanel.switchClass("hide", "show", 300, "easeInOutQuad");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });

                $('html, body').animate({
                    scrollTop: $("#task-list-panel").offset().top
                }, 1000);
            }
        })
    });
    $('ul.showtaskTabs .task_request').addClass('tab-current');
        $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
            $('#dependent-fields').hide();
        }
    })

    $('#show-new-task-panel').click(function () {
        editTaskPanel.addClass('hide').removeClass('show');
        newTaskpanel.switchClass("hide", "show", 300, "easeInOutQuad");

        $('html, body').animate({
            scrollTop: $("#task-list-panel").offset().top
        }, 1000);
    });

    $('#hide-new-task-panel').click(function () {
        newTaskpanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");

    });

    editTaskPanel.on('click', '#hide-edit-task-panel', function () {
        editTaskPanel.addClass('hide').removeClass('show');
        taskListPanel.switchClass("col-md-6", "col-md-12", 1000, "easeInOutQuad");

    });
            Dropzone.autoDiscover = false;
            //Dropzone class
            myDropzone = new Dropzone("div#file-upload-dropzone", {
                url: "{{ route('client.task-request-files.store') }}",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                paramName: "file",
                maxFilesize: 10,
                maxFiles: 10,
                acceptedFiles: "image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                autoProcessQueue: false,
                uploadMultiple: true,
                addRemoveLinks: true,
                parallelUploads: 10,
                dictDefaultMessage: "@lang('modules.projects.dropFile')",
                init: function () {
                    myDropzone = this;
                    this.on("success", function (file, response) {
                        console.log(response);
                        var newDropzone = this;
                        newDropzone.removeAllFiles( true );

                        if(response.status == 'fail') {
                            $.showToastr(response.message, 'error');
                            return;
                        }
                    })
                }
            });

            myDropzone.on('sending', function (file, xhr, formData) {
                console.log(myDropzone.getAddedFiles().length, 'sending');
                var ids = $('#taskID').val();
                formData.append('task_id', ids);
            });

            myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.taskCreatedSuccessfully')";
            $.showToastr(msgs, 'success');
            showTable();
            
        });
</script>
@endpush
