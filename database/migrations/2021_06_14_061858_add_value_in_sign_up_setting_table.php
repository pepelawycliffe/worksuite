<?php

use App\SignUpSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddValueInSignUpSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $signUp = new SignUpSetting();
        $signUp->language_setting_id = null;
        $signUp->message = 'Registration is currently closed. Please try again later. If you have any inquiries feel free to contact us';
        $signUp->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_up_setting', function (Blueprint $table) {
            //
        });
    }
}
