@if($global->system_update == 1)
    @php($updateVersionInfo = \Froiden\Envato\Functions\EnvatoUpdate::updateVersionInfo())
    @if(isset($updateVersionInfo['lastVersion']))
        <div class="alert alert-info col-md-12">
            <div class="col-md-10"><i class="ti-gift"></i> @lang('modules.update.newUpdate') <label
                        class="label label-success">{{ $updateVersionInfo['lastVersion'] }}</label></div>
            <div class="col-md-2"><a href="{{ route('super-admin.update-settings.index') }}"
                                     class="btn btn-success btn-small">@lang('modules.update.updateNow') <i
                            class="fa fa-arrow-right"></i></a></div>
        </div>
    @endif
@endif