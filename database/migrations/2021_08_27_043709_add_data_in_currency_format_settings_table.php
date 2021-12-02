<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\CurrencyFormatSetting;

class AddDataInCurrencyFormatSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = \App\Company::select('id')->get();
        if($companies){
            foreach ($companies as $company) {
                $currencyFormatSetting = new CurrencyFormatSetting();
                $currencyFormatSetting->currency_position = 'left';
                $currencyFormatSetting->company_id = $company->id;
                $currencyFormatSetting->no_of_decimal = '2';
                $currencyFormatSetting->thousand_separator = ',';
                $currencyFormatSetting->decimal_separator = '.';
                $currencyFormatSetting->sample_data = '123456.78$';
                $currencyFormatSetting->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_format_settings', function (Blueprint $table) {
            //
        });
    }
}
