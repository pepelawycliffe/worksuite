<?php

namespace App;

class RoleUser extends BaseModel
{
    public $timestamps = false;

    protected $table = 'role_user';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'user_id');
    }

}
