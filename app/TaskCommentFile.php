<?php

namespace App;

use App\Observers\TaskCommentFileObserver;
use App\Scopes\CompanyScope;

class TaskCommentFile extends BaseModel
{

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('task-files/' . $this->task_id . '/' . $this->hashname);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskCommentFileObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
