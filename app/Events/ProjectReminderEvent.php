<?php

namespace App\Events;

use App\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectReminderEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $projectsArr;
    public $projectData;
    public $notifyUser;

    public function __construct($projectsArr, $notifyUser, $projectData)
    {
        $this->projectsArr = $projectsArr;
        $this->projectData = $projectData;
        $this->notifyUser = $notifyUser;
    }

}
