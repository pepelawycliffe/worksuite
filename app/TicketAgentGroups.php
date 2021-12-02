<?php

namespace App;

use App\Observers\TicketAgentGroupObserver;
use App\Scopes\CompanyScope;

class TicketAgentGroups extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(TicketAgentGroupObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'agent_id')->withoutGlobalScopes(['active']);
    }

    public function group()
    {
        return $this->belongsTo(TicketGroup::class, 'group_id');
    }

}
