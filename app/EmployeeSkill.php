<?php

namespace App;

class EmployeeSkill extends BaseModel
{
    protected $table = 'employee_skills';

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
