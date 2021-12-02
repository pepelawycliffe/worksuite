<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleCalendarModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_calendar_modules', function (Blueprint $table) {
            $table->id();
            // Relationships.
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            // Data.
            $table->boolean('lead_status')->default(0);
            $table->boolean('leave_status')->default(0);
            $table->boolean('invoice_status')->default(0);
            $table->boolean('contract_status')->default(0);
            $table->boolean('task_status')->default(0);
            $table->boolean('event_status')->default(0);
            $table->boolean('holiday_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_accounts');
    }
}
