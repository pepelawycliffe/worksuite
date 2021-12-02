<?php

namespace App;

use App\Observers\InvoiceSettingObserver;
use App\Scopes\CompanyScope;

class InvoiceSetting extends BaseModel
{
    protected $appends = ['logo_url','is_chinese_lang'];

    public function getLogoUrlAttribute()
    {
        return (is_null($this->logo)) ? $this->company->logo_url : asset_url('app-logo/' . $this->logo);
    }

    public function getIsChineseLangAttribute()
    {
        return in_array(strtolower($this->locale), ['zh-hk', 'zh-cn', 'zh-sg', 'zh-tw', 'cn']);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(InvoiceSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

}
