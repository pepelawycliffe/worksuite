<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayfastColumnInStripeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_setting', function (Blueprint $table) {
            $table->string('payfast_key')->nullable();
            $table->string('payfast_secret')->nullable();
            $table->string('payfast_webhook_secret')->nullable();
            $table->enum('payfast_status', ['active', 'inactive'])->default('inactive');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_setting', function (Blueprint $table) {
            //
        });
    }
}
