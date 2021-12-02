<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeColumnInPaymentAndStripeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->dropColumn('payfast_webhook_secret');
            $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');

        });

        Schema::table('stripe_setting', function (Blueprint $table) {
            $table->dropColumn('payfast_webhook_secret');
            $table->enum('payfast_mode', ['sandbox', 'live'])->default('sandbox');

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
