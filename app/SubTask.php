<?php

namespace App;

use App\Observers\SubTaskObserver;

class SubTask extends BaseModel
{
    protected $dates = ['due_date'];

    protected static function boot()
    {
        parent::boot();

        static::observe(SubTaskObserver::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function files()
    {
        return $this->hasMany(SubTaskFile::class, 'sub_task_id');
    }

}
