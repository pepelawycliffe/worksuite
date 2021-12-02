<?php

namespace App\Listeners;

use App\Events\NewProjectEvent;
use App\Events\ProjectReminderEvent;
use App\Notifications\ProjectReminder;
use Illuminate\Support\Facades\Notification;

class ProjectReminderListener
{

    /**
     * NewExpenseRecurringListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param NewProjectEvent $event
     */
    public function handle(ProjectReminderEvent $event)
    {
        Notification::send($event->notifyUser, new ProjectReminder($event->projectsArr, [
            'company' => $event->projectData['company'], 'project_setting' => $event->projectData['project_setting'],
        ]));
    }

}
