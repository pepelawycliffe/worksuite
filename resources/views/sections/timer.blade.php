@if(in_array('timelogs',$modules))
<div class="row">
<div class="col-md-12">
<span id="timer-section">
    @if(!is_null($timer))
        <div class="nav navbar-top-links navbar-right pull-right m-t-10 m-b-0">
            <a class="btn btn-rounded btn-default stop-timer-modal" href="javascript:;" data-timer-id="{{ $timer->id }}">
                <i class="ti-alarm-clock"></i>
                <span id="active-timer">{{ $timer->timer }}</span>
                <label class="label label-danger">@lang("app.stop")</label></a>
        </div>
    @else
        <div class="nav navbar-top-links navbar-right pull-right m-t-10 m-b-0">
            <a class="btn btn-outline btn-inverse timer-modal" href="javascript:;">@lang("modules.timeLogs.startTimer") <i class="fa fa-check-circle text-success"></i></a>
        </div>
    @endif
</span>
@if(isset($activeTimerCount) && $user->cans('view_timelogs'))
<span id="timer-section">
    <div class="nav navbar-top-links navbar-right m-t-10 m-r-10">
        <a class="btn btn-rounded btn-default active-timer-modal" href="javascript:;">@lang("modules.projects.activeTimers")
            <span class="label label-danger" id="activeCurrentTimerCount">@if($activeTimerCount > 0) {{ $activeTimerCount }} @else 0 @endif</span>
        </a>
    </div>
</span>
@endif
</div>
</div>
@endif