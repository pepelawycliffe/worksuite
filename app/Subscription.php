<?php

namespace App;

class Subscription extends BaseModel
{
    protected $dates = ['created_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
