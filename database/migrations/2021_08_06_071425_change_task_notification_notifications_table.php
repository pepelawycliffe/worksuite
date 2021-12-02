<?php
use App\Notification;
use App\Task;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTaskNotificationNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifiData = ['App\Notifications\NewTask', 'App\Notifications\TaskUpdated', 'App\Notifications\TaskComment',
        'App\Notifications\TaskCommentClient', 'App\Notifications\TaskCompleted', 'App\Notifications\NewClientTask'];    
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
                    $task = Task::find($dt->id);
                    if($task){
                        $value->data = $task->toArray();
                    }else{
                        $value->read_at = Carbon::now();   
                    }
                    $value->save();
                
                }            
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
        //
    }
}
