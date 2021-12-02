<div class="row">

   <div class="col-xs-1">
        <img src="{{ $row->user->image_url }}" class="img-circle" width="35" height="35" />
   </div>

    <div class="col-xs-8">
        <h5 class="font-semi-bold p-t-0 m-t-0">

            @if (!is_null($row->project_id))
                <a href="{{ route('admin.projects.discussionReplies', [$row->project_id, $row->id]) }}" class="text-dark">{{ ucwords($row->title) }}</a>
            @else
                <a href="{{ route('admin.discussion.show', [$row->id]) }}" class="text-dark">{{ ucwords($row->title) }}</a>
            @endif

        </h5>

        @if(!is_null($row->last_reply_by_id))
            <a href="{{ route('admin.employees.show', $row->last_reply_by_id) }}">{{ ucwords($row->last_reply_by->name) }}</a>
        @endif

        @if (count($row->replies) > 1)
            @lang('modules.discussions.replied')
        @else
            @lang('modules.discussions.posted')
        @endif

            <span class="text-muted font-semi-bold">{{ $row->last_reply_at->timezone($global->timezone)->format($global->date_format . ' ' . $global->time_format) }}</span>

            </div>

        <div class="col-xs-1">
        <span class="font-semi-bold font-medium"><i class="fa fa-comment"></i> {{ count($row->replies) }} </span>
        </div>

        <div class="col-xs-2 text-right">
        <span style="color: {{ $row->category->color }}"><i class="fa fa-circle"></i> {{ ucwords($row->category->name) }}</span>
        <div class="action-div m-t-20"><a href="javascript:;" data-discussion-id="{{ $row->id }}" class="text-muted delete-discussion"><i class=" fa fa-trash"></i> @lang('app.delete') </a></div>
        </div>
    </div>
@if(count($row->files) > 0)
<div class="row">
    <ul>
        @forelse($row->files as $file)
            <li class="list-group-item" id="discussion-file-{{  $file->id }}">
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
                            <a href="{{ route('admin.discussion-files.download', $file->id) }}"
                               data-toggle="tooltip"  data-original-title="Download"
                               class="btn btn-success btn-circle text-success"><i
                                        class="fa fa-download text-success"></i></a>
                        @endif

                        <a href="javascript:;" data-toggle="tooltip"  data-original-title="Delete" data-file-id="{{ $file->id }}"
                           data-pk="list" class="btn btn-danger btn-circle file-delete"><i class="fa fa-times"></i></a>

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
@endif