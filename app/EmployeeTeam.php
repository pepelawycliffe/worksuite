<?php

namespace App;

class EmployeeTeam extends BaseModel
{

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

}
