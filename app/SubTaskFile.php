<?php

namespace App;

use App\Observers\SubTaskFileObserver;
use App\Scopes\CompanyScope;

class SubTaskFile extends BaseModel
{

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('sub-task-files/' . $this->sub_task_id . '/' . $this->hashname);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(SubTaskFileObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
