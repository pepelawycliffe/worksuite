<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCurrencyIdInPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $packages = \App\Package::whereNull('currency_id')->get();
        $globaleSetting = \App\GlobalSetting::first();
        if ($packages  && $globaleSetting) {
            foreach ($packages as $package) {
                $package->currency_id = $globaleSetting->currency_id;
                $package->save();
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
