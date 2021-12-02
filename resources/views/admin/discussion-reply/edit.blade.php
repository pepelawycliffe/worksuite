<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('app.edit') @lang('app.reply')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'createProjectCategory','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <div class="row">

                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.reply')</label>
                        <textarea id="description" name="description" class="form-control summernote">{{ $reply->body }}</textarea>
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
                            <input type="hidden" name="discussionID" id="discussionID">
                        @else
                            <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-category" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>

<script>
    @if($upload)
        Dropzone.autoDiscover = false;
    //Dropzone class
    myDropzone = new Dropzone("div#file-upload-dropzone", {
        url: "{{ route('admin.discussion-files.store') }}",
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
                if(response.status == 'fail') {
                    $.showToastr(response.message, 'error');
                    return;
                }
            })
        }
    });

    myDropzone.on('sending', function (file, xhr, formData) {
        console.log(myDropzone.getAddedFiles().length, 'sending');
        var ids = $('#discussionID').val();
        formData.append('discussion_id', ids);
    });

    myDropzone.on('completemultiple', function () {
        var msgs = "@lang('messages.taskCreatedSuccessfully')";
        $.showToastr(msgs, 'success');
        window.location.href = '{{ route('admin.projects.discussionReplies', [$reply->discussion->project_id, $reply->discussion_id]) }}'

    });
    @endif
    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]],
        ]
    });

    $('#save-category').click(function () {
        $.easyAjax({
            url: '{{route('admin.discussion-reply.update', $reply->id)}}',
            container: '#createProjectCategory',
            type: "POST",
            data: $('#createProjectCategory').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    var dropzone = 0;
                    @if($upload)
                        dropzone = myDropzone.getQueuedFiles().length;
                    @endif

                    if(dropzone > 0){
                        replyID = response.replyID;
                        $('#discussionID').val(response.replyID);
                        myDropzone.processQueue();
                    } else {
                        var msgs = "@lang('messages.taskCreatedSuccessfully')";
                        $.showToastr(msgs, 'success');
                        $('#discussion-replies').html(response.html);
                        $('#editTimeLogModal').modal('hide');
                        window.location.href = '{{ route('admin.projects.discussionReplies', [$reply->discussion->project_id, $reply->discussion_id]) }}'
                    }
                }
            }
        })
    });
</script>