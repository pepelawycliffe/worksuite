<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleEventIdToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_follow_up', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('events', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });
        Schema::table('holidays', function (Blueprint $table) {
            $table->text('event_id')->nullable();
        });

        Schema::table('global_settings', function (Blueprint $table) {
            $table->enum('google_calendar_status',['active', 'inactive'])->default('inactive');
            $table->text('google_client_id')->nullable();
            $table->text('google_client_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        Schema::table('holidayS', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
}
