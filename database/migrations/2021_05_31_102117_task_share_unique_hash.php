<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TaskShareUniqueHash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        config(['app.seeding' => true]);
        if (!Schema::hasColumn('tasks', 'hash')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('hash',64)->nullable();
            });
        }
        \DB::statement('UPDATE tasks set hash=MD5(RAND())');
//        $tasks = \App\Task::whereNull('hash')->withoutGlobalScopes()->get();
//        foreach ($tasks as $task) {
//            $task->hash = \Illuminate\Support\Str::random(32);
//            $task->save();
//        }
        config(['app.seeding' => false]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
}
