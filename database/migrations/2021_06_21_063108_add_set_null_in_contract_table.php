<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSetNullInContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `contracts` DROP FOREIGN KEY `contracts_contract_type_id_foreign`;');
        DB::statement('ALTER TABLE `contracts` CHANGE `contract_type_id` `contract_type_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
        DB::statement('ALTER TABLE `contracts` ADD  CONSTRAINT `contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            //
        });
    }
}
