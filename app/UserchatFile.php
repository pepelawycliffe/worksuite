<?php

namespace App;

use App\Observers\UserChatFileObserver;
use App\Scopes\CompanyScope;

class UserchatFile extends BaseModel
{
    protected $appends = ['file_url', 'icon'];
    protected $table = 'users_chat_files';

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('user-chat-files/' . $this->hashname);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(UserChatFileObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function chat()
    {
        return $this->belongsTo(UserChat::class, 'users_chat_id');
    }

}
