<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SignUpSetting extends Model
{

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
