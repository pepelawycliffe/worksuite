<?php

namespace App;

class StripeSetting extends BaseModel
{
    protected $table = 'stripe_setting';

    protected $appends = ['show_pay'];

    public function getShowPayAttribute()
    {

        return in_array('active', [
            $this->attributes['paypal_status'],
            $this->attributes['stripe_status'],
            $this->attributes['paystack_status'],
            $this->attributes['razorpay_status'],
            $this->attributes['mollie_status'],
            $this->attributes['authorize_status'],
            $this->attributes['payfast_status']
        ]);
    }

}
