<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/daterange-picker/daterangepicker.css') }}" />

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.tasks.subTask')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        {!! Form::open(['id'=>'createSubTask','class'=>'ajax-form','method'=>'POST']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.name')</label>
                        <input type="text" name="name" id="name" class="form-control">
                        <input type="hidden" name="taskID" id="taskID" value="{{ $taskID }}">
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-md-6 " id="duedateBox_subtask">
                        <div class="form-group">
                            <label>@lang('app.dueDate')</label>
                            <input type="text" name="due_date" autocomplete="off" id="due_date4" value="" class="form-control datepicker">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="padding-top: 25px;">
                            <div class="checkbox checkbox-info">
                                <input id="without_duedate_subtask" name="without_duedate" value="true"
                                       type="checkbox">
                                <label for="without_duedate_subtask">@lang('modules.tasks.withoutDuedate')</label>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.description')</label>
                        <textarea name="description" class="form-control" rows="4"> </textarea>
                    </div>
                </div>
            </div>
            <div class="row m-b-20">
                <div class="col-xs-12">
                    @if($upload)
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
                        <input type="hidden" name="subTaskID" id="subTaskID">
                    @else
                        <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" onclick="saveSubTaskWithFile()" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script type="text/javascript" src="{{ asset('plugins/daterange-picker/daterangepicker.js') }}"></script>

<script>
    $('#due_date4').daterangepicker({
        singleDatePicker: true,
        autoApply: true,
        locale: {
            language: '{{ $global->locale }}',
            format: '{{ $global->moment_format }}',
        },
    });
    $('#without_duedate_subtask').click(function () {
        var check = $('#without_duedate_subtask').is(":checked") ? true : false;
        if(check == true){
            $('#duedateBox_subtask').hide();
            $('#due_date4').val('');
        }
        else{
            $('#duedateBox_subtask').show();
        }
    });
 var subTaskId = '';

    @if($upload)
        Dropzone.autoDiscover = false;
    //Dropzone class
    mySubTaskDropzone = new Dropzone("div#file-upload-dropzone", {
        url: "{{ route('admin.sub-task-files.store') }}",
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
                // if(response.status == 'fail') {
                //     $.showToastr(response.message, 'error');
                //     return;
                // }
                $('.subTaskFileDiv'+response.subTaskId).after(response.html)
            })
        }
    });

    mySubTaskDropzone.on('sending', function (file, xhr, formData) {
        console.log(mySubTaskDropzone.getAddedFiles().length, 'sending');
        var ids = $('#subTaskID').val();
        formData.append('sub_task_id', ids);
    });

    mySubTaskDropzone.on('completemultiple', function () {
        var msgs = "@lang('messages.subTaskAdded')";
        // $.showToastr(msgs, 'success');
        $('#subTaskModal').modal('hide');
    });
    @endif

    function saveSubTaskWithFile() {
        $.easyAjax({
            url: '{{route('admin.sub-task.store')}}',
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            disableButton: true,
            success: function (response) {
            $(".btn-success").prop('disabled', true);

                // $('#storeTask').trigger("reset");
                // $('.summernote').summernote('code', '');
                var dropzone = 0;
                @if($upload)
                    dropzone = mySubTaskDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                    subTaskID = response.subTaskID;
                    $('#subTaskID').val(response.subTaskID);
                    mySubTaskDropzone.processQueue();
                } else {
                    var msgs = "@lang('messages.subTaskAdded')";
                    // $.showToastr(msgs, 'success');
                    {{--window.location.href = '{{ route('admin.all-tasks.index') }}'--}}
                }
                if (response.status == "success") {
                    $('#percentage-count').html(`<span class="pull-right"><span class="donut">${response.data.completedSubtasks}/${response.data.totalSubTasks}</span> <span class="text-muted font-12">${response.data.percentageTaskCompleted}%</span></span>`);

                    $('#subtask-count').text("@lang('modules.tasks.subTask')" +'('+(response.data.totalSubTasks)+')');
                    $('#sub-task-list').html(response.view)

                }
                $('#subTaskModal').modal('hide');

            }
        })
    }

</script>
