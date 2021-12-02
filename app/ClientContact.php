<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\ClientContactObserver;

class ClientContact extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ClientContactObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
