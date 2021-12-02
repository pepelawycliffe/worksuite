<?php

namespace App\Observers;

use App\Booking;
use App\Notification;
use App\BookingTime;
use App\Event;
use App\EventAttendee;
use Google_Service_Calendar_Event;
use App\Services\Google;

class EventObserver
{

    public function saving(Event $event)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $event->company_id = company()->id;
        }
    }

    public function deleting(Event $event)
    {
        $notifiData = ['App\Notifications\EventInvite'];

        $notifications = Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$event->id.',%')
            ->delete();

    }

}
