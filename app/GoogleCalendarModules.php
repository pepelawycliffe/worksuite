<?php

namespace App;

class GoogleCalendarModules extends BaseModel
{
    protected $table = 'google_calendar_modules';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
