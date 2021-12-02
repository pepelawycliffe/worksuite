<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<style>
    select.bs-select-hidden, select.selectpicker {
        display: block!important;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading "><i class="ti-pencil"></i> @lang('modules.tasks.updateTask')
        <div class="panel-action">
            <a href="javascript:;" class="close" id="hide-edit-task-panel" data-dismiss="modal"><i class="ti-close"></i></a>
        </div>
    </div>
    <div class="panel-wrapper collapse in">
        <div class="panel-body">
            {!! Form::open(['id'=>'updateTask','class'=>'ajax-form','method'=>'PUT']) !!}
            {!! Form::hidden('project_id', $task->project_id) !!}

            <div class="form-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label required">@lang('app.heading')</label>
                            <input type="text" id="heading" name="title" class="form-control" value="{{ $task->heading }}">
                        </div>
                    </div>
                    <!--/span-->
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label">@lang('app.description')</label>
                            <textarea id="description" name="description" class="form-control summernote">{{ $task->description }}</textarea>
                        </div>
                    </div>
                    <!--/span-->
                    <div class="col-md-3">
                        <div class="form-group">

                            <div class="checkbox checkbox-info">
                                <input id="billable-task-2" name="billable" value="true"
                                       @if ($task->billable)
                                       checked
                                       @endif
                                       type="checkbox">
                                <label for="billable-task-2">@lang('modules.tasks.billable') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.billableInfo')</span></span></span></a></label>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="form-group">

                            <div class="checkbox checkbox-info">
                                <input id="dependent-task-project" name="dependent" value="yes"
                                       type="checkbox" @if($task->dependent_task_id != '') checked @endif onclick="dependedSelected(this)">
                                <label for="dependent-task-project">@lang('modules.tasks.dependent')</label>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dependent-fields-project" @if($task->dependent_task_id == null) style="display: none" @endif>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseTask')" name="dependent_task_id" id="dependent_task_id_project" >
                                    @foreach($allTasks as $allTask)
                                        <option value="{{ $allTask->id }}" @if($allTask->id == $task->dependent_task_id) selected @endif>{{ $allTask->heading }} (@lang('app.dueDate'): {{$allTask->due_date != '' ?$allTask->due_date->format($global->date_format) :'' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label required">@lang('app.startDate')</label>
                            <input type="text" name="start_date" id="start_date2" class="form-control" autocomplete="off" value="@if($task->start_date != '-0001-11-30 00:00:00' && $task->start_date != null) {{ $task->start_date->format($global->date_format) }} @endif">
                        </div>
                    </div>
                    <!--/span-->
                    <div class="col-md-4" id="duedateBox2">
                        <div class="form-group">
                            <label class="control-label required">@lang('app.dueDate')</label>
                            <input type="text" name="due_date" id="due_date2" class="form-control" autocomplete="off" value="@if($task->due_date != '-0001-11-30 00:00:00') {{$task->due_date != '' ? $task->due_date->format($global->date_format):'' }} @endif">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="padding-top: 20px;">
                            <div class="checkbox checkbox-info">
                                    <input id="without_duedate2" @if($task->due_date == null) checked @endif  name="without_duedate" value="true"
                                       type="checkbox">
                                <label for="without_duedate2">@lang('modules.tasks.withoutDuedate')</label>
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
                                    <option value="{{ $category->id }}"
                                            @if($task->task_category_id == $category->id)
                                            selected
                                            @endif
                                    >{{ ucwords($category->category_name) }}</option>
                                @empty
                                    <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.tasks.priority')</label>

                            <div class="radio radio-danger">
                                <input type="radio" name="priority" id="radio13"
                                       @if($task->priority == 'high') checked @endif
                                       value="high">
                                <label for="radio13" class="text-danger">
                                    @lang('modules.tasks.high') </label>
                            </div>
                            <div class="radio radio-warning">
                                <input type="radio" name="priority"
                                       @if($task->priority == 'medium') checked @endif
                                       id="radio14" value="medium">
                                <label for="radio14" class="text-warning">
                                    @lang('modules.tasks.medium') </label>
                            </div>
                            <div class="radio radio-success">
                                <input type="radio" name="priority" id="radio15"
                                       @if($task->priority == 'low') checked @endif
                                       value="low">
                                <label for="radio15" class="text-success">
                                    @lang('modules.tasks.low') </label>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-20">
                        <div class="col-xs-12">
                            @if($upload)
                                <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                <div id="file-upload-box" >
                                    <div class="row" id="file-dropzone-edit">
                                        <div class="col-xs-12">
                                            <div class="dropzone"
                                                 id="file-upload-dropzone-edit">
                                                {{ csrf_field() }}
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                                <input name="image_url" id="image_url_edit"type="hidden" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="taskID" id="taskID">
                            @else
                                <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                            @endif
                        </div>
                    </div>
                    <div class="row" id="list">
                        <ul class="list-group" id="files-list">
                            @forelse($task->files as $file)
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-9">
                                            {{ $file->filename }}
                                        </div>
                                        <div class="col-md-3">

                                                <a target="_blank" href="{{ $file->file_url }}"
                                                   data-toggle="tooltip" data-original-title="View"
                                                   class="btn btn-info btn-circle"><i
                                                            class="fa fa-search"></i></a>

                                            @if(is_null($file->external_link))
                                                &nbsp;&nbsp;
                                                <a href="{{ route('client.task-request-files.download', $file->id) }}"
                                                   data-toggle="tooltip" data-original-title="Download"
                                                   class="btn btn-inverse btn-circle"><i
                                                            class="fa fa-download"></i></a>
                                            @endif
                                            &nbsp;&nbsp;
                                            <a href="javascript:;" data-toggle="tooltip"
                                               data-original-title="Delete"
                                               data-file-id="{{ $file->id }}"
                                               class="btn btn-danger btn-circle sa-params file-delete" data-pk="list"><i
                                                        class="fa fa-times"></i></a>

                                            <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-10">
                                            @lang('messages.noFileUploaded')
                                        </div>
                                    </div>
                                </li>
                            @endforelse

                        </ul>
                    </div>

                </div>
                <!--/row-->

            </div>
            <div class="form-actions">
                <button type="button" id="update-task" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
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

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script>

    $("#dependent_task_id_project, #user_id2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    //    update task
    $('#update-task').click(function () {
        var currentStatus =  $('#status').val();
            $.easyAjax({
                url: '{{route('client.tasks-request.check-task', [$task->id])}}',
                type: "GET",
                data: {},
                success: function (data) {
                    if(data.taskCount > 0){
                        swal({
                            title: "@lang('messages.sweetAlertTitle')",
                            text: "@lang('messages.confirmation.markTaskComplete')",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "@lang('messages.completeIt')",
                            cancelButtonText: "@lang('messages.confirmNoArchive')",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                updateTask();
                            }
                        });
                    }
                    else{
                        updateTask();
                    }

                }
            });
    });
  
    function updateTask(){
        $.easyAjax({
            url: '{{route('client.tasks-request.update', [$task->id])}}',
            container: '#updateTask',
            type: "POST",
            data: $('#updateTask').serialize(),
            success: function (data) {
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                    taskID = data.taskID;
                    $('#taskID').val(data.taskID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.taskCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                   showTable();
                    // return;
                }
                $('#task-list-panel ul.list-group').html(data.html);

                $('#edit-task-panel').switchClass("show", "hide", 300, "easeInOutQuad");
                showTable();
            }
        })
    }
    @if($upload)
        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("div#file-upload-dropzone-edit", {
            url: "{{ route('client.task-request-files.store') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            dictDefaultMessage: "@lang('modules.projects.dropFile')",
            init: function () {

                // myDropzone = this;
                this.on("success", function (file, response) {
                    if(response.status == 'fail') {
                        $.showToastr(response.message, 'error');
                        return;
                    }
                })
            }
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            console.log(myDropzone.getAddedFiles().length,'sending');
            var ids = '{{ $task->id }}';
            formData.append('task_id', ids);
        });

        myDropzone.on('completemultiple', function () {
        var msgs = "@lang('messages.taskUpdatedSuccessfully')";
        $.showToastr(msgs, 'success');
        showTable();


    });
    @endif
    jQuery('#due_date2, #start_date2').datepicker({
        format: '{{ $global->date_picker_format }}',
        autoclose: true,
        todayHighlight: true
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

    $('#dependent-task-project').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields-project').show();
        }
        else{
            $('#dependent-fields-project').hide();
        }
    })
        @if($task->due_date == null)
        $('#duedateBox2').hide();
    @endif
    $('#without_duedate2').click(function () {
            var check = $('#without_duedate2').is(":checked") ? true : false;
            if(check == true){
                $('#duedateBox2').hide();
            }
            else{
            $('#duedateBox2').show();
            }
        });
        $('body').on('click', '.file-delete', function() {
        var id = $(this).data('file-id');
        swal({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.confirmation.deleteFile')",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "@lang('messages.confirmNoArchive')",
                confirm: {
                    text: "@lang('messages.deleteConfirmation')!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function(isConfirm) {
            if (isConfirm) {

                var url = "{{ route('client.task-request-files.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#task-file-' + id).remove();
                            $('#totalUploadedFiles').html(response.totalFiles);
                            $('#list ul.list-group').html(response.html);
                        }
                    }
                });
            }
        });
    });
    
</script>
