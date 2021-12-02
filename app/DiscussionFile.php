<?php

namespace App;

use App\Observers\DiscussionFileObserver;
use App\Scopes\CompanyScope;

class DiscussionFile extends BaseModel
{
    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('discussion-files/' . $this->hashname);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(DiscussionFileObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class, 'discussion_id');
    }

}
