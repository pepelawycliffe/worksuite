<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class AlterCategoryIdExpenseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `expenses` DROP FOREIGN KEY `expenses_category_id_foreign`;');
        DB::statement('ALTER TABLE `expenses` ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expenses_category`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;');

        DB::statement('ALTER TABLE `expenses_recurring` DROP FOREIGN KEY `expenses_recurring_category_id_foreign`;');
        DB::statement('ALTER TABLE `expenses_recurring` ADD CONSTRAINT `expenses_recurring_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expenses_category`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;');
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
