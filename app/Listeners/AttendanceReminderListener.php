<?php

namespace App\Listeners;

use App\Events\AttendanceReminderEvent;
use App\Notifications\AttendanceReminder;
use Illuminate\Support\Facades\Notification;

class AttendanceReminderListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param AttendanceReminderListener $event
     * @return void
     */
    public function handle(AttendanceReminderEvent $event)
    {
        Notification::send($event->notifyUser, new AttendanceReminder($event));
    }

}
