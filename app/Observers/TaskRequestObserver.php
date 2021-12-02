<?php

namespace App\Observers;

use App\TaskRequest;

class TaskRequestObserver
{

    public function saving(TaskRequest $taskRequest)
    {
        if (company()) {
            $taskRequest->company_id = company()->id;
        }
    }

}