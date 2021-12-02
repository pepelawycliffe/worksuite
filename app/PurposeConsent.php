<?php

namespace App;

use App\Observers\PurposeConsentObserver;
use App\Scopes\CompanyScope;

class PurposeConsent extends BaseModel
{
    protected $table = 'purpose_consent';
    protected $fillable = ['name', 'description'];

    protected static function boot()
    {
        parent::boot();

        static::observe(PurposeConsentObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function lead()
    {
        return $this->hasOne(PurposeConsentLead::class, 'purpose_consent_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(PurposeConsentUser::class, 'purpose_consent_id', 'id');
    }

}
