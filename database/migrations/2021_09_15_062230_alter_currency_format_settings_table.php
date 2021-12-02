<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\CurrencyFormatSetting;

class AlterCurrencyFormatSettingsTable extends Migration
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
                CurrencyFormatSetting::where('company_id',$company->id)->update(
                    ['sample_data' => '1,234,567.89']
                );
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
        //
    }
}
