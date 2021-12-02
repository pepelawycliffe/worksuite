<?php

namespace App\Observers;

use App\Events\SubTaskCompletedEvent;
use App\SubTask;
use App\Notification;

class SubTaskObserver
{

    public function created(SubTask $subTask)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new SubTaskCompletedEvent($subTask, 'created'));
        }
    }

    public function updated(SubTask $subTask)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($subTask->isDirty('status') && $subTask->status == 'complete') {
                event(new SubTaskCompletedEvent($subTask, 'completed'));
            }
        }
    }

    public function deleting(SubTask $subTask)
    {
        $notifiedData = [
            'App\Notifications\SubTaskCompleted',
            'App\Notifications\SubTaskCreated'
        ];
        Notification::whereIn('type', $notifiedData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$subTask->id.',%')
            ->delete();
    }

}
