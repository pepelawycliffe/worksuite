<?php

namespace App;

use App\Observers\RemovalRequestObserver;
use App\Scopes\CompanyScope;

class RemovalRequest extends BaseModel
{

    protected $table = 'removal_requests';

    protected static function boot()
    {
        parent::boot();

        static::observe(RemovalRequestObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
