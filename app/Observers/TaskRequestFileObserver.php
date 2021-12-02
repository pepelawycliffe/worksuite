<?php

namespace App\Observers;

use App\TaskRequestFile;

class TaskRequestFileObserver
{

    public function saving(TaskRequestFile $file)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $file->company_id = company()->id;
        }
    }

}
