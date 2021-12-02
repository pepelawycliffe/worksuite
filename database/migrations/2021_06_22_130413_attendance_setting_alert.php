<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttendanceSettingAlert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->integer('alert_after')->nullable()->default(null)->after('ip_check');
            $table->boolean('alert_after_status')->default(1)->after('alert_after');
        });

        Schema::table('employee_details', function (Blueprint $table) {
            $table->date('attendance_reminder')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn('alert_after');
            $table->dropColumn('alert_after_status');
        });

        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn('attendance_reminder');
        });
    }
}
