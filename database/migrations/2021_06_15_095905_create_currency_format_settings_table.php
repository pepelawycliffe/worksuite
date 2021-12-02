<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\CurrencyFormatSetting;

class CreateCurrencyFormatSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_format_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('currency_position', ['left', 'right', 'left_with_space', 'right_with_space'])->default('left');
            $table->integer('no_of_decimal')->unsigned();
            $table->string('thousand_separator')->nullable();
            $table->string('decimal_separator')->nullable();
        });

        $currencyFormatSetting = new CurrencyFormatSetting();
        $currencyFormatSetting->currency_position = 'left';
        $currencyFormatSetting->no_of_decimal = '2';
        $currencyFormatSetting->thousand_separator = ',';
        $currencyFormatSetting->decimal_separator = '.';
        $currencyFormatSetting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_format_settings');
    }
}
