<?php

namespace App;

class Module extends BaseModel
{
    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->hasMany('App\Permission', 'module_id');
    }

}
