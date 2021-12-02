@foreach($comments as $comment)
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

        @foreach($comment->comment_file as $file)
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
@endforeach