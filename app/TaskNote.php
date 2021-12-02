<?php

namespace App;

use App\Observers\TaskNoteObserver;
use App\Scopes\CompanyScope;

class TaskNote extends BaseModel
{

    protected static function boot()
    {
        parent::boot();
        static::observe(TaskNoteObserver::class);
        static::addGlobalScope(new CompanyScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

}
