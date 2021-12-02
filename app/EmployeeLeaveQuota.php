<?php

namespace App;

use App\Observers\EmployeeLeaveQuotaObserver;
use App\Scopes\CompanyScope;

class EmployeeLeaveQuota extends BaseModel
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeLeaveQuotaObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

}
