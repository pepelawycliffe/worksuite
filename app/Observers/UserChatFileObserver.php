<?php

namespace App\Observers;

use App\UserchatFile;

class UserChatFileObserver
{

    public function saving(UserchatFile $file)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $file->company_id = company()->id;
        }
    }

}
