<?php

namespace App;

class PaystackSubscription extends BaseModel
{
    protected $dates = ['created_at'];

    protected $table = 'paystack_subscriptions';

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
