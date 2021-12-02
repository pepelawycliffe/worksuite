<?php

namespace App\Observers;

use App\Attendance;
use App\Notification;

class AttendanceObserver
{

    public function saving(Attendance $attendance)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $attendance->company_id = company()->id;
        }
    }

    public function deleting(Attendance $attendance)
    {
        $notifiData = ['App\Notifications\AttendanceReminder'];

        $notifications = Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$attendance->id.',%')->delete();
    }

}
