@forelse($comments as $comment)
    <div class="row b-b m-b-5 m-t-10  font-12">
        <div class="col-xs-12">
            <h5>{{ ucwords($comment->user->name) }}
                <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span>
            </h5>
        </div>
        <div class="col-xs-12">
            <div class="row m-b-10">
                <div class="col-md-6">{!! ucfirst($comment->comment)  !!}</div>
                <div class="col-md-6 text-right">
                    <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteComment('{{ $comment->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
                </div>
            </div>
         
            @if(!is_null($comment->comment_file))
            @foreach ($comment->comment_file as $file )
                <li class="list-group-item m-t-10" style="border-top: none;"  id="task-comment-file-{{  $file->id }}">
                    <div class="row">
                        <div class="col-md-9">
                            {{ $file->filename }}
                        </div>
                        <div class="col-md-3 text-right">
                                <a target="_blank" href="{{ $file->file_url }}"
                                data-toggle="tooltip" data-original-title="View"
                                class="btn btn-info btn-circle"><i
                                            class="fa fa-search"></i></a>
                            @if(is_null($file->external_link))
                            <a href="{{ route('admin.task-comment.download', $file->id) }}"
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
        </div>
        
        {{-- <div class="col-xs-2 text-right">
            <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="btn btn-xs  btn-outline btn-default" onclick="deleteComment('{{ $comment->id }}');return false;"><i class="fa fa-trash"></i> @lang('app.delete')</a>
        </div> --}}
    </div>
@empty
    <div class="col-xs-12">
        @lang('messages.noRecordFound')
    </div>
@endforelse
