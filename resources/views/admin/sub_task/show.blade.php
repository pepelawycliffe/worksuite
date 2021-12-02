@foreach($subTasks as $subtask)
    @php $subTaskCount = (isset($subtask->files)) ? $subtask->files->count() : 0; @endphp
    <li class="list-group-item row subTaskFileDiv{{$subtask->id}} @if($subTaskCount > 0) nonBottomBorder @endif">
        <div class="col-xs-9">
            <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                       @if($subtask->status == 'complete') checked @endif>
                <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                <span>{{ ucfirst($subtask->title) }}</span>
            </div>
            @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
        </div>

        <div class="col-xs-3 text-right">
            <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" title="@lang('app.edit')" class="edit-sub-task"><i class="fa fa-pencil"></i></a>&nbsp;
            <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}"  title="@lang('app.delete')"  class="delete-sub-task"><i class="fa fa-trash"></i></a>
        </div>
        <div class="row" id="fileList"></div>
        <div class="row" id="fileList{{$subtask->id}}"></div>

        @forelse($subtask->files as  $key => $file)

            <li class="list-group-item sub-task-file nonTopBorder @if($subTaskCount != ($key+1)) nonBottomBorder @endif" id="sub-task-file-{{  $file->id }}">
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
                            <a href="{{ route('admin.sub-task-files.download', $file->id) }}"
                               data-toggle="tooltip" data-original-title="Download"
                               class="btn btn-inverse btn-circle"><i
                                        class="fa fa-download"></i></a>
                        @endif

                        <a href="javascript:;" data-toggle="tooltip"  data-original-title="Delete" data-file-id="{{ $file->id }}"
                           data-pk="list" class="btn btn-danger btn-circle task-file-delete"><i class="fa fa-times"></i></a>

                    </div>
                </div>
            </li>
        @empty

        @endforelse
    </li>

@endforeach
