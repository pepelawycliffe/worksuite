<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\NotesObserver;

class Notes extends BaseModel
{
    protected $table = 'notes';
   
    protected static function boot()
    {
        parent::boot();
        static::observe(NotesObserver::class);
        static::addGlobalScope(new CompanyScope);
    }
    
    public function member()
    {
        return $this->hasMany(ClientUserNotes::class, 'note_id');
    }
   
}