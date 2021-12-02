<?php

namespace App;

class TicketTag extends BaseModel
{
    protected $guarded = ['id'];

    public function tag()
    {
        return $this->belongsTo(TicketTagList::class, 'tag_id');
    }

}
