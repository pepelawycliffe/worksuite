<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNullInPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `packages` CHANGE `annual_price` `annual_price` decimal UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `packages` CHANGE `monthly_price` `monthly_price` decimal UNSIGNED NULL DEFAULT NULL');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            //
        });
    }
}
