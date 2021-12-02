<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.tasks.subTask')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        {!! Form::open(['id'=>'createSubTask','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.name')</label>
                        <input type="text" name="name" id="name" value="{{ $subTask->title }}" class="form-control">
                        <input type="hidden" name="taskID" id="taskID" value="{{ $subTask->task_id }}">
                    </div>
                </div>
                    <div class="col-md-6" id="duedateBox_subtask">
                        <div class="form-group">
                            <label>@lang('app.dueDate')</label>
                            <input type="text" name="due_date" autocomplete="off" @if( $subTask->due_date) value="{{ $subTask->due_date->format($global->date_format) }}" @endif id="due_date" class="form-control datepicker">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group " style="padding-top: 25px;">
                            <div class="checkbox checkbox-info">
                                <input id="without_duedate_edit" name="without_duedate" value="true" @if($subTask->due_date == null) checked @endif
                                       type="checkbox">
                                <label for="without_duedate_edit">@lang('modules.tasks.withoutDuedate')</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 ">
                        <div class="form-group">
                            <label>@lang('app.description')</label>
                            <textarea name="description" class="form-control" rows="4"> {!! $subTask->description !!}  </textarea>
                        </div>
                    </div>
                <div class="row m-b-20">
                    <div class="col-xs-12">
                        @if($upload)
                            <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                            <div id="file-upload-box" >
                                <div class="row" id="file-dropzone">
                                    <div class="col-xs-12">
                                        <div class="dropzone"
                                             id="file-upload-dropzone">
                                            {{ csrf_field() }}
                                            <div class="fallback">
                                                <input name="file" type="file" multiple/>
                                            </div>
                                            <input name="image_url" id="image_url"type="hidden" />
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
                <div class="row" id="subTasklist">
                    <ul class="list-group" id="files-list">
                        @forelse($subTask->files as $file)
                            <li class="list-group-item sub-task-file" id="sub-task-file-{{ $file->id }}">
                                <div class="row">
                                    <div class="col-md-9">
                                        {{ $file->filename }}
                                    </div>
                                    <div class="col-md-3">
                                        <a target="_blank" href="{{ $file->file_url }}"
                                           data-toggle="tooltip" data-original-title="View"
                                           class="btn btn-info btn-circle"><i
                                                    class="fa fa-search"></i></a>
                                        &nbsp;&nbsp;
                                        <a href="javascript:;" data-toggle="tooltip"
                                           data-original-title="Delete"
                                           data-file-id="{{ $file->id }}"
                                           class="btn btn-danger btn-circle task-file-delete" data-pk="list"><i
                                                    class="fa fa-times"></i></a>
                                        @if(is_null($file->external_link))
                                            <a href="{{ route('member.sub-task-memberfiles.download', $file->id) }}"
                                            data-toggle="tooltip" data-original-title="Download"
                                            class="btn btn-inverse btn-circle"><i
                                                        class="fa fa-download"></i></a>
                                        @endif
                                        <div class ="row">
                                        <span class="m-l-11">{{ $file->created_at->diffForHumans() }}</span>
                                        </div>
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
        </div>
        <div class="form-actions">
            <button type="button" onclick="updateSubTaskWithFile({{$subTask->id}})" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    jQuery('#due_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    //due date box
    @if(is_null($subTask->due_date))
        $('#due_date').val('');
        $('#duedateBox_subtask').hide();
     @endif

     $('#without_duedate_edit').click(function () {
         var check = $('#without_duedate_edit').is(":checked") ? true : false;
         if(check == true){
             $('#duedateBox_subtask').hide();
             $('#due_date').val('');
         }
         else{
             $('#duedateBox_subtask').show();
         }
     });

    var subTaskID = '';
     @if($upload)
         Dropzone.autoDiscover = false;
     //Dropzone class
     mySubTaskDropzone = new Dropzone("div#file-upload-dropzone", {
         url: "{{ route('member.sub-task-memberfiles.store') }}",
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
             mySubTaskDropzone = this;
             this.on("success", function (file, response) {
                //  if(response.status == 'fail') {
                //      $.showToastr(response.message, 'error');
                //      return;
                //  }
                $('.subTaskFileDiv'+response.subTaskId).after(response.html)
             })
         }
     });

     mySubTaskDropzone.on('sending', function(file, xhr, formData) {
         console.log(mySubTaskDropzone.getAddedFiles().length,'sending');
         var ids = '{{ $subTask->id }}';
         formData.append('sub_task_id', ids);
     });

     mySubTaskDropzone.on('completemultiple', function () {
         $.showToastr(msgs, 'success');
         $('#subTaskModal').modal('hide');
     });
     @endif
     
     function updateSubTaskWithFile(id) {
         var url = '{{ route('member.sub-task.update', ':id')}}';
         url = url.replace(':id', id);
         $.easyAjax({
             url: url,
             container: '#createSubTask',
             type: "POST",
            disableButton: true,
             data: $('#createSubTask').serialize(),
             success: function (response) {
            $(".btn-success").prop('disabled', true);
                 var dropzone = 0;
                 @if($upload)
                     dropzone = mySubTaskDropzone.getQueuedFiles().length;
                 @endif

                 if(dropzone > 0){
                     subTaskID = response.subTaskID;
                     $('#subTaskID').val(response.subTaskID);
                     mySubTaskDropzone.processQueue();
                 }
                 else{
                     var msgs = "@lang('messages.taskCreatedSuccessfully')";
                     $.showToastr(msgs, 'success');
                     $('#subTaskModal').modal('hide');
                 }

                 $('#sub-task-list').html(response.view)
                 $('#subTaskModal').modal('hide');
                 $('body').removeClass('modal-open');
                 $('.modal-backdrop').remove();
             }
         })
     }
</script>