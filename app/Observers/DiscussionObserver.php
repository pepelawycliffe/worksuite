<?php

namespace App\Observers;

use App\Discussion;
use App\Events\DiscussionEvent;
use App\Events\NewUserEvent;
use App\User;
use App\Notification;

class DiscussionObserver
{

    public function saving(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (company()) {
                $discussion->company_id = company()->id;
            }
        }
    }

    public function created(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new DiscussionEvent($discussion));
        }
    }

    public function deleting(Discussion $discussion)
    {
        $notifiData = ['App\Notifications\NewDiscussion','App\Notifications\NewDiscussionReply'];

        $notifications = Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$discussion->id.',%')
            ->delete();
    }

}
