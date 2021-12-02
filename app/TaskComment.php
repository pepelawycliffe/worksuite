<?php

namespace App;

use App\Observers\TaskCommentObserver;

class TaskComment extends BaseModel
{

    protected static function boot()
    {
        parent::boot();
        static::observe(TaskCommentObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function comment_file()
    {
        return $this->hasMany(TaskCommentFile::class, 'comment_id');
    }

}
