<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('heading');
            $table->mediumText('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('task_category_id')->unsigned()->nullable()->default(null);
            $table->foreign('task_category_id')->references('id')->on('task_category')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->integer('column_priority');
            $table->integer('created_by')->unsigned()->nullable()->default(null);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('dependent_task_id')->unsigned()->nullable()->default(null);
            $table->foreign('dependent_task_id')->references('id')->on('task_requests')->onDelete('set null')->onUpdate('cascade');
            $table->boolean('billable')->default(1);
            $table->enum('request_status', ['pending', 'approve','rejected'])->default('pending');
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
        Schema::dropIfExists('task_requests');
    }
}
