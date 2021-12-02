<?php

namespace App;

use App\Observers\PaymentGatewayCredentialObserver;
use App\Scopes\CompanyScope;

class PaymentGatewayCredentials extends BaseModel
{
    protected $appends = ['show_pay'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PaymentGatewayCredentialObserver::class);

        static::addGlobalScope(new CompanyScope);

    }

    public function getShowPayAttribute()
    {
        return in_array('active', [$this->attributes['paypal_status'],$this->attributes['stripe_status'], $this->attributes['paystack_status'], $this->attributes['razorpay_status'], $this->attributes['mollie_status'], $this->attributes['authorize_status'], $this->attributes['payfast_status']]);
    }

}
