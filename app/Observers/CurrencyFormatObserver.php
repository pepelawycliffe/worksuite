<?php

namespace App\Observers;

use App\CurrencyFormatSetting;

class CurrencyFormatObserver
{

    public function saving(CurrencyFormatSetting $currency)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $currency->company_id = company()->id;
        }
        session()->forget('company_currency_position');
    }

}
