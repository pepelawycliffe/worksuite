<?php

namespace App;

use App\Observers\DiscussionReplyObserver;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionReply extends BaseModel
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::observe(DiscussionReplyObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function files()
    {
        return $this->hasMany(DiscussionFile::class, 'discussion_reply_id');
    }

}
