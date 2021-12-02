<?php

namespace App;

use App\Observers\InvoiceRecurringObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;

class RecurringInvoice extends BaseModel
{
    use Notifiable;

    protected $table = 'invoice_recurring';
    protected $dates = ['issue_date', 'due_date'];
    protected $appends = ['total_amount', 'issue_on'];

    protected static function boot()
    {
        parent::boot();

        static::observe(InvoiceRecurringObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function recurrings()
    {
        return $this->hasMany(Invoice::class, 'invoice_recurring_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes(['active']);
    }

    public function withoutGlobalScopeCompanyClient()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes([CompanyScope::class, 'active']);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function clientdetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(RecurringInvoiceItems::class, 'invoice_recurring_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withoutGlobalScopes(['enable']);
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->total) && !is_null($this->currency_symbol)) {
            return $this->currency_symbol . $this->total;
        }

        return '';
    }

    public function getIssueOnAttribute()
    {
        if (!is_null($this->issue_date)) {
            return Carbon::parse($this->issue_date)->format('d F, Y');
        }
        return '';
    }

    public function getUpcomingDate(){
        $company = company();
        // Why type of date is today
        $today = Carbon::now()->timezone($company->timezone);
        $lastCreatedInvoice = Invoice::where('invoice_recurring_id', $this->id)->orderBy('created_at', 'desc')->first();

        if($this->unlimited_recurring == 1 || ($this->unlimited_recurring == 0 && $this->recurrings->count() <= $this->billing_cycle))
        {
            if($lastCreatedInvoice){
                $lastCreated = $lastCreatedInvoice->created_at;
                if($this->rotation === 'daily' && !is_null($lastCreated)){
                    $nextDay = $lastCreated->addDay();
                }
                elseif ($this->rotation === 'weekly' && !is_null($lastCreated)){
                    $nextDay = $lastCreated->addWeek();
                }
                elseif ($this->rotation === 'bi-weekly' && !is_null($lastCreated)){
                    $nextDay = $lastCreated->addWeeks(2);
                }
                elseif ($this->rotation === 'monthly' && !is_null($lastCreated)){
                    $nextDay = $lastCreated->addMonth();
                }
                elseif ($this->rotation === 'quarterly' && !is_null($lastCreated))
                {
                    $nextDay = $lastCreated->addMonths(3);
                }
                elseif ($this->rotation === 'half-yearly' && !is_null($lastCreated))
                {
                    $nextDay = $lastCreated->addMonths(6);
                }
                elseif($this->rotation === 'annually' && !is_null($lastCreated))
                {
                    $nextDay = $lastCreated->addYear();
                }
            }
            else{
                $created = $this->created_at;
                if($this->rotation === 'daily'){
                    $nextDay = $today->addDay();
                }
                elseif($this->rotation === 'weekly'){
                    $weekDate =  $created->startOfWeek()->addDays((($this->day_of_week - $today->dayOfWeek) + 1));
                    $nextDay =  ($today->greaterThan($weekDate)) ? $created->addWeek()->startOfWeek()->addDays((($this->day_of_week - $today->dayOfWeek) + 1)): $weekDate;
                }
                elseif($this->rotation === 'bi-weekly'){
                    $biweekDate =  $created->addWeeks(2)->startOfWeek();
                    $nextDay =  $biweekDate->addDays((($this->day_of_week - $biweekDate->dayOfWeek) + 1));
                }
                elseif($this->rotation === 'monthly'){
                    $createDate = $created->addMonth();
                    $nextDay = $createDate->startOfMonth()->addDays(($this->day_of_month - 1));

                }elseif($this->rotation === 'quarterly'){
                    $createDate1 = $created->addMonths(3);
                    $nextDay =  $createDate1->startOfMonth()->addDays(($this->day_of_month - 1));
                }elseif($this->rotation === 'half-yearly'){
                    $createDate2 = $created->addMonths(6);
                    $nextDay =  $createDate2->startOfMonth()->addDays(($this->day_of_month - 1));
                }else{
                    $createDate3 = $created->addYear();
                    $nextDay = $createDate3->startOfMonth()->addDays(($this->day_of_month - 1));
                }
            }
        }

        if($nextDay){
            return $nextDay->format($company->date_format);
        }

        return null;
    }

}
