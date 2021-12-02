<style>
    .modal-form{
        position: fixed;
    z-index: 1;
    width: 70%;
    }
    </style>
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<div class="rpanel-title modal-form"> @lang('app.task') <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body">

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ ucwords($task->heading) }}</h3>
        </div>
        <div class="col-xs-6">
            <label for="">@lang('modules.tasks.assignTo')</label><br>
            @foreach ($task->users as $item)
                <img src="{{ $item->image_url }}" data-toggle="tooltip" title="{{ ucwords($item->name) }}" data-original-title="{{ ucwords($item->name) }}" data-placement="right" class="img-circle" width="25" height="25" alt="">
            @endforeach
        </div>
        <div class="col-xs-6">
            <label for="">@lang('app.dueDate')</label><br>
            @if(!is_null($task->due_date))
                 <span @if($task->due_date->isPast()) class="text-danger" @endif>{{ $task->due_date->format($global->date_format) }}</span>
            @endif
        </div>
        <div class="col-xs-12 task-description">
            {!! ucfirst($task->description) !!}
        </div>


        <div class="col-xs-12 m-t-20 m-b-10">
            <ul class="list-group" id="sub-task-list">
                @foreach($task->subtasks as $subtask)
                    <li class="list-group-item row">
                        <div class="col-xs-9">
                            <span>{{ ucfirst($subtask->title) }}</span>
                        </div>

                        <div class="col-xs-3 text-right">
                            @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
                        </div>
                    </li>
                @endforeach

            </ul>

            <div class="row b-all m-t-10 p-10"  id="new-sub-task" style="display: none">
                <div class="col-xs-11 ">
                    <a href="javascript:;" id="create-sub-task" data-name="title"  data-url="{{ route('admin.sub-task.store') }}" class="text-muted" data-type="text"></a>
                </div>

                <div class="col-xs-1 text-right">
                    <a href="javascript:;" id="cancel-sub-task" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                </div>
            </div>

        </div>

        <div class="col-xs-12 m-t-15">
            <h5>@lang('modules.tasks.comment')</h5>
        </div>

        <div class="col-xs-12" id="comment-container">
            <div id="comment-list">
                @forelse($task->comments as $comment)
                    <div class="row  font-12">
                        <div class="col-xs-12">
                            <h5>{{ ucwords($comment->user->name) }} <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span></h6>
                        </div>
                        <div class="col-xs-10">
                            {!! ucfirst($comment->comment)  !!}
                        </div>
                        @if($comment->user_id == $user->id)
                        <div class="col-xs-2 text-right">
                            <a href="javascript:;" data-comment-id="{{ $comment->id }}" onclick="deleteComment('{{ $comment->id }}')" class="text-danger">@lang('app.delete')</a>
                        </div>
                        @endif
                    </div>
                    @if(!is_null($comment->comment_file))
                    @foreach ($comment->comment_file as $file )
                       <li class="list-group-item" id="task-comment-file-{{  $file->id }}">
                           <div class="row">
                               <div class="col-md-6">
                                   {{ $file->filename }}
                               </div>
                               <div class="col-md-3">
                                   <span class="">{{ $file->created_at->diffForHumans() }}</span>
                               </div>
                               <div class="col-md-3">
                                       <a target="_blank" href="{{ $file->file_url }}"
                                          data-toggle="tooltip" data-original-title="View"
                                          class="btn btn-info btn-circle"><i
                                                   class="fa fa-search"></i></a>
                                   @if(is_null($file->external_link))
                                   <a href="{{ route('client.task-comment.download', $file->id) }}"
                                      data-toggle="tooltip" data-original-title="Download"
                                      class="btn btn-inverse btn-circle"><i
                                               class="fa fa-download"></i></a>
                                   @endif

                                   <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete" data-file-id="{{ $file->id }}"
                                      data-pk="list" class="btn btn-danger btn-circle comment-file-delete"><i class="fa fa-times"></i></a>

                               </div>
                           </div>
                       </li>
                       @endforeach
                       @endif
                @empty
                    <div class="col-xs-12">
                        @lang('messages.noRecordFound')
                    </div>
                @endforelse
            </div>
            <ul class="list-group" id="comment-files-list">
                
             </ul>
        </div>

        <div class="form-group" id="comment-box">
            
            <div class="col-xs-12">
                <textarea name="comment" id="task-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                <button href="javascript:;" id="show-dropzone-comment"
                        class="btn btn-success btn-sm btn-outline  m-b-20"><i class="ti-upload"></i> @lang('modules.projects.uploadImage')</button>
                        <div class="row m-b-20 hide" id="file-dropzone-comment">
                            <div class="col-xs-12">
                                @if($upload)
                                <div class="dropzone"
                                    id="file-upload-dropzone-comment">
                                    {{ csrf_field() }}
                                    <div class="fallback">
                                        <input name="file" type="file" multiple/>
                                    </div>
                                    <input name="image_url" id="image_url"type="hidden" />
                                </div>
                                <input type="hidden" name="taskID" id="taskID">         
                                <input type="hidden" name="commentID" id="commentID">         
                            @else
                                <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                            @endif
    
                            </div>
                        </div>
            </div>
            <div class="col-xs-3">
                <a href="javascript:;" id="submit-comment" class="btn btn-success"><i class="fa fa-send"></i> @lang('app.submit')</a>
            </div>
        </div>

    </div>

</div>

<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>
<script>
    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,                 // set focus to editable area after initializing summernote,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']]
        ]
    });
    @if($upload)

Dropzone.autoDiscover = false;
//Dropzone class
myDropzone = new Dropzone("#file-upload-dropzone-comment", {
    url: "{{ route('client.task-comment.comment-file') }}",
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
        myDropzone = this;
        this.on("success", function (file, response) {
            if(response.status == 'fail') {
                $.showToastr(response.message, 'error');
                return;
            }
         
                $('#comment-list').html(response.view);
                // $('#files-list-panel ul.list-group').html(response.html);
                
                $('.summernote').summernote("reset");
                $('.dz-preview dz-image-preview').html('');
                $('#task-comment').val('');
                this.removeAllFiles();
              
          
        })
    }
});

myDropzone.on('sending', function(file, xhr, formData) {
    console.log(myDropzone.getAddedFiles().length,'sending');
    var ids = $('#taskID').val();
    var comment_id = $('#commentID').val();
    formData.append('task_id', ids);
    formData.append('comment_id',comment_id);
});

myDropzone.on('completemultiple', function () {
    var msgs = "@lang('messages.taskCreatedSuccessfully')";
    $.showToastr(msgs, 'success');
   // window.location.href = '{{ route('member.all-tasks.index') }}'

});
@endif
    //    change sub task status

    $('#submit-comment').click(function () {
        var comment = $('#task-comment').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("client.task-comment.store") }}',
            type: "POST",
            data: {'_token': token, comment: comment, taskId: '{{ $task->id }}'},
            success: function (response) {
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                   
                    taskID = response.taskID;
                    commentID = response.commentID;
                    $('#taskID').val(response.taskID);
                    $('#commentID').val(response.commentID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.taskCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                }
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                    $('.summernote').summernote("reset");
                    $('.note-editable').html('');
                    $('#task-comment').val('');
                }
            }
        })
    })
 
        $('#show-dropzone-comment').click(function () {
            $('#file-dropzone-comment').toggleClass('hide show');
            myDropzone.removeAllFiles();

        });
    function deleteComment(id) {
        var commentId = id;
        var token = '{{ csrf_token() }}';

        var url = '{{ route("client.task-comment.destroy", ':id') }}';
        url = url.replace(':id', commentId);

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, '_method': 'DELETE', commentId: commentId},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                }
            }
        })
    }
    $('body').on('click', '.comment-file-delete', function () {
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
        }).then(function (isConfirm) {
            if (isConfirm) {
                var url = "{{ route('client.task-comment.comment-file-delete',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#task-comment-file-'+id).remove();
                            $('#totalUploadedFiles').html(response.totalFiles);
                            $('#comment-files-list').html(response.html);
                            $('#list ul.list-group').html(response.html);
                        }
                    }
                });
            }
        });
    });

</script>
