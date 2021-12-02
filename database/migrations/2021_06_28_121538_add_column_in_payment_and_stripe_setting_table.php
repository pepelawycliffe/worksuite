<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInPaymentAndStripeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->string('payfast_salt_passphrase')->nullable();
        });

        Schema::table('stripe_setting', function (Blueprint $table) {
            $table->string('payfast_salt_passphrase')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_and_stripe_setting', function (Blueprint $table) {
            //
        });
    }
}
