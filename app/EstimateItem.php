<?php

namespace App;

class EstimateItem extends BaseModel
{
    protected $guarded = ['id'];

    public static function taxbyid($id)
    {
        return Tax::where('id', $id);
    }

}
