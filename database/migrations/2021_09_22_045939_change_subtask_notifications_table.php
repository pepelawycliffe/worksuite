<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Notification;
use App\SubTask;
use Carbon\Carbon;

class ChangeSubtaskNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifiData = ['App\Notifications\SubTaskCreated', 'App\Notifications\SubTaskCompleted'];    
        $notifications = Notification::
        whereIn('type', $notifiData)
        ->whereNull('read_at')
        ->get();

        foreach ($notifications as $key => $value) {
            if($value->data)
            {
                $dt = json_decode($value->data);

                if($dt && isset($dt->id))
                {
                    if(isset($dt->task_id)) {
                        $task = SubTask::where('task_id', $dt->id)->first();
                    }else{
                        $task = SubTask::where('id', $dt->id)->first();
                    }
                    if(!is_null($task)){
                        $value->data = $task->toArray();
                    }else{
                        $value->read_at = Carbon::now();
                    }
                    $value->save();
                }            
            }
            else{
                $value->read_at = Carbon::now();
                $value->save();
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
        Schema::table('notifications', function (Blueprint $table) {
            //
        });
    }
}
