@forelse($chatDetails as $chatDetail)

    <li class="@if($chatDetail->from == $user->id) odd @else  @endif">
        <div class="chat-image">
            @if(is_null($chatDetail->fromUser->image))
                <img src="{{ asset('img/default-profile-3.png') }}" alt="user-img"
                     class="img-circle" style="height:30px; width:30px;">
            @else
                <img src="{{ asset_url('avatar/' . $chatDetail->fromUser->image) }}" alt="user-img"
                     class="img-circle" style="height:30px; width:30px;">
            @endif
        </div>
        <div class="chat-body">
            <div class="chat-text">
                @if(($chatDetail->from == $user->id))
                    <div class="messageDelete @if($chatDetail->from == $user->id) left @else right @endif" onclick="deleteMessage('{{ $chatDetail->id }}')"><i class="fa fa-trash"></i></div>
                @endif
                <h4>@if($chatDetail->from == $user->id) you @else {{$chatDetail->fromUser->name}} @endif</h4>
                <p>{{ $chatDetail->message }} </p>
                    @foreach($chatDetail->files as $file)
                        <div class="col-md-2 m-b-10">
                            <div class="card">
                                <div class="file-bg">
                                    <div class="overlay-file-box">
                                        <div class="user-content">
                                            <a target="_blank" href="{{ $file->file_url }}">
                                                @if($file->icon == 'images')
                                                    <img class="card-img-top img-responsive" src="{{ $file->file_url }}" alt="Card image cap">
                                                @else
                                                    <i class="fa {{$file->icon}} card-img-top img-responsive" style="font-size: -webkit-xxx-large; padding-top: 65px;"></i>
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <h6 class="card-title thumbnail-img"> </h6>

                                    <a target="_blank" href="{{ $file->file_url }}"
                                       data-toggle="tooltip" data-original-title="View"
                                       class="btn btn-info btn-circle"><i
                                                class="fa fa-search"></i></a>

                                        <a href="{{ route('admin.user-chat-files.download', $file->id) }}"
                                           data-toggle="tooltip" data-original-title="Download"
                                           class="btn btn-default btn-circle"><i
                                                    class="fa fa-download"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                <b>{{ $chatDetail->created_at->timezone($global->timezone)->format($global->date_format.' '. $global->time_format) }}</b>
            </div>
        </div>
    </li>

@empty
    <li><div class="message">@lang('messages.noMessage')</div></li>
@endforelse
