@if(!is_null($global->last_cron_run))
    @if(\Carbon\Carbon::now()->diffInHours($global->last_cron_run) > 48)
        <div class="clearfix"></div>
        <div class="col-md-12">

            <div class="alert alert-danger alert-dismissable">
                @lang('messages.cronIsNotRunning')
            </div>

        </div>
    @endif
@else
    <div class="clearfix"></div>
    <div class="col-md-12">

        <div class="alert alert-danger alert-dismissable" id="{{$global->last_cron_run}}">
            @lang('messages.cronIsNotRunning')
        </div>

    </div>
@endif