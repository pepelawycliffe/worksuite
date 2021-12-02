<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<style>
    /* .dropzone .dz-preview .dz-remove {
        display: none;
    } */

    .nonBottomBorder {
        border-bottom: 0;
    }

    .nonTopBorder {
        border-top: 0;
    }

</style>
<div class="rpanel-title"> @lang('app.task') <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body p-t-0">

    <div class="row">
        <div class="col-xs-12 col-md-9 p-t-20 b-r h-scroll">
            <div class="col-xs-12">
            </div>
            <div class="col-xs-12">
                <h4>
                    {{ ucwords($task->heading) }}
                </h4>
                @if (!is_null($task->project_id))
                    <p><i class="icon-layers"></i> {{ ucfirst($task->project->project_name) }}</p>
                @endif

                <h5>
                    @if ($task->task_category_id)
                        <label
                            class="label label-default text-dark font-light">{{ ucwords($task->category->category_name) }}</label>
                    @endif

                    <label class="font-light label
                    @if ($task->priority == 'high') label-danger
                @elseif($task->priority == 'medium') label-warning @else label-success @endif
                    ">
                    <span class="text-dark">@lang('modules.tasks.priority') ></span>
                    {{ ucfirst($task->priority) }}
                </label>
            </h5>

        </div>

        <ul class="nav customtab nav-tabs" role="tablist">
            <li role="presentation" class=""><a href="#messages1" aria-controls="messages" role="tab"
                    data-toggle="tab" aria-expanded="false">@lang('app.file') (<span
                        id="totalUploadedFiles">{{ sizeof($task->files) }}</span>) </a></li>
        </ul>
            <div class="col-xs-12">
                <ul class="list-group" id="files-list">
                    @forelse($task->files as $file)
                        <li class="list-group-item" id="task-file-{{ $file->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ $file->filename }}
                                </div>
                                <div class="col-md-3">
                                    <span class="">{{ $file->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="col-md-3">
                                    <a target="_blank" href="{{ $file->file_url }}" data-toggle="tooltip"
                                        data-original-title="View" class="btn btn-info btn-circle"><i
                                            class="fa fa-search"></i></a>
                                    @if (is_null($file->external_link))
                                        <a href="{{ route('admin.task-request.download', $file->id) }}"
                                            data-toggle="tooltip" data-original-title="Download"
                                            class="btn btn-inverse btn-circle"><i
                                                class="fa fa-download"></i></a>
                                    @endif

                                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Delete"
                                        data-file-id="{{ $file->id }}" data-pk="list"
                                        class="btn btn-danger btn-circle file-delete"><i
                                            class="fa fa-times"></i></a>

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
        <div class="tab-content" id="task-detail-section">
            <div role="tabpanel" class="tab-pane fade active in" id="home1">

                <div class="col-xs-12">
                    @if ($task->users)
                        <div class="row visible-xs visible-sm">
                            <div class="col-xs-6 col-md-3 font-12">
                                <label class="font-12" for="">@lang('modules.tasks.assignTo')</label><br>
                                @foreach ($task->users as $item)
                                    <img src="{{ $item->image_url }}" data-toggle="tooltip"
                                        data-original-title="{{ ucwords($item->name) }}" data-placement="right"
                                        class="img-circle" width="25" height="25" alt="">
                                @endforeach
                            </div>
                            @if ($task->create_by)
                                <div class="col-xs-6 col-md-3 font-12">
                                    <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                                    <img src="{{ $task->create_by->image_url }}" class="img-circle" width="25"
                                        height="25" alt="">

                                    {{ ucwords($task->create_by->name) }}
                                </div>
                            @endif

                            @if ($task->start_date)
                                <div class="col-xs-6 col-md-3 font-12">
                                    <label class="font-12" for="">@lang('app.startDate')</label><br>
                                    <span
                                        class="text-success">{{ $task->start_date->format($global->date_format) }}</span><br>
                                </div>
                            @endif
                            @if ($task->due_date)
                                <div class="col-xs-6 col-md-3 font-12">
                                    <label class="font-12" for="">@lang('app.dueDate')</label><br>
                                    <span @if ($task->due_date->isPast()) class="text-danger" @endif>
                                        {{ $task->due_date->format($global->date_format) }}
                                    </span>
                                    <span style="color: {{ $task->board_column->label_color }}"
                                        id="columnStatus"> {{ $task->board_column->column_name }}</span>

                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="row">

                        <div class="col-xs-12 col-md-12 m-t-10">
                            <label class="font-bold" for="">@lang('app.description')</label><br>
                            <div class="task-description m-t-10">
                                {!! ucfirst($task->description) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <div class="col-xs-6 col-md-3 hidden-xs hidden-sm">

        <div class="row">
            <div class="col-xs-12 p-10 p-t-20 ">
                <label class="font-12" for="">@lang('app.status')</label><br>
                @if ($task->request_status == 'pending')
                    <label class="label label-warning"> {{ ucfirst($task->request_status) }} </label>
                @elseif($task->request_status == 'approve')
                    <label class="label label-success"> {{ ucfirst($task->request_status) }} </label>
                @elseif ($task->request_status == 'rejected')
                    <label class="label label-danger"> {{ ucfirst($task->request_status) }} </label>
                @endif
            </div>
            @if ($task->users)
                <div class="col-xs-12">
                    <hr>

                    <label class="font-12" for="">@lang('modules.tasks.assignTo')</label><br>
                    @foreach ($task->users as $item)
                        <img src="{{ $item->image_url }}" data-toggle="tooltip"
                            data-original-title="{{ ucwords($item->name) }}" data-placement="right"
                            class="img-circle" width="35" height="35" alt="">
                    @endforeach
                    <hr>
                </div>
            @endif
            @if ($task->create_by)
                <div class="col-xs-12">
                    <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                    <img src="{{ $task->create_by->image_url }}" class="img-circle" width="35" height="35"
                        alt="">

                    {{ ucwords($task->create_by->name) }}
                    <hr>
                </div>
            @endif

            @if ($task->start_date)
                <div class="col-xs-12  ">
                    <label class="font-12" for="">@lang('app.startDate')</label><br>
                    <span class="text-success">{{ $task->start_date->format($global->date_format) }}</span><br>
                    <hr>
                </div>
            @endif
            @if ($task->due_date)
                <div class="col-xs-12 ">
                    <label class="font-12" for="">@lang('app.dueDate')</label><br>
                    <span @if ($task->due_date->isPast()) class="text-danger" @endif>
                        {{ $task->due_date->format($global->date_format) }}
                    </span>
                    <hr>
                </div>
            @endif

        </div>

    </div>
</div>

</div>

<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>


<script>
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

                var url = "{{ route('admin.task-request.delete-file', ':id') }}";
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
