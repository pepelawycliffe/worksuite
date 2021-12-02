<li class="top-notifications">
    <div class="message-center">
        @if(isset($notification->data['hash']))
        <a href="{{route('front.task-share',[$notification->data['hash']])}}" target="_blank">
            <div class="user-img">
                <span class="btn btn-circle btn-success"><i class="fa fa-tasks"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">@lang('email.subTaskComplete.subject')</span>
                @if(isset($notification->data['heading']))
                    <small>{{ ucfirst($notification->data['heading']) }}</small>
                @else
                    <small>{{ ucfirst($notification->data['title']) }}</small>
                @endif
                <span class="time">@if(isset($notification->created_at) && !is_null($notification->created_at)){{ \Carbon\Carbon::parse( $notification->data['completed_on'])->diffForHumans() }}@endif</span>
            </div>
        </a>
        @else
            <div class="user-img">
                <span class="btn btn-circle btn-success"><i class="fa fa-tasks"></i></span>
            </div>
            <div class="mail-contnet">
                <span class="mail-desc m-0">@lang('email.subTaskComplete.subject')</span>
                @if(isset($notification->data['heading']))
                    <small>{{ ucfirst($notification->data['heading']) }}</small>
                @else
                    <small>{{ ucfirst($notification->data['title']) }}</small>
                @endif
                <span class="time">@if(isset($notification->created_at) && !is_null($notification->created_at)){{ \Carbon\Carbon::parse( $notification->data['completed_on'])->diffForHumans() }}@endif</span>
            </div>
        @endif
    </div>
</li>
