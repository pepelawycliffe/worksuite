<?php

namespace App\Observers;

use App\TaskCommentFile;

class TaskCommentFileObserver
{

    public function saving(TaskCommentFile $file)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $file->company_id = company()->id;
        }
    }

}
