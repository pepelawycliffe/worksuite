<?php

namespace App\Http\Controllers\SuperAdmin;

use App\GlobalSetting;
use App\Helper\Reply;
use App\Http\Requests\SuperAdmin\GoogleCalendar\UpdateRequest;
use Illuminate\Http\Request;

class GoogleCalendarSettingsController extends SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.googleCalendarSetting';
        $this->pageIcon = 'icon-settings';
    }

    public function index()
    {
        $this->calendarSetting = GlobalSetting::first();
        return view('super-admin.google-calendar-settings.index', $this->data);
    }

    public function update(UpdateRequest $request,  $id)
    {
        $setting = GlobalSetting::first();
        $setting->google_client_id = $request->google_client_id;
        $setting->google_client_secret = $request->google_client_secret;
        $setting->google_calendar_status = $request->google_calendar_status;
        $setting->save();
        cache()->forget('global_setting');

        return Reply::success(__('messages.settingsUpdated'));
    }

}
