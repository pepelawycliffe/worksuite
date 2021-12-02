<?php

namespace App;

use App\Observers\NoticeViewObserver;
use App\Scopes\CompanyScope;

class NoticeView extends BaseModel
{
    protected $dates = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::observe(NoticeViewObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes([CompanyScope::class, 'active']);
    }

}
