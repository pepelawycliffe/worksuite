<?php

use App\PusherSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePusherSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pusher_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('pusher_app_id')->nullable();
            $table->string('pusher_app_key')->nullable();
            $table->string('pusher_app_secret')->nullable();
            $table->string('pusher_cluster')->nullable();
            $table->boolean('force_tls');
            $table->boolean('status');
            $table->boolean('message_status')->default(0);
            $table->boolean('taskboard_status')->default(0);
            $table->timestamps();
        });

        $companies = \App\Company::select('id')->get();
        if($companies){
            foreach ($companies as $company) {
                $pusherSetting = new PusherSetting();
                $pusherSetting->company_id = $company->id;
                $pusherSetting->save();
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
        Schema::dropIfExists('pusher_settings');
    }
}
