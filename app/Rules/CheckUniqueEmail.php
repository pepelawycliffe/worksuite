<?php

namespace App\Rules;

use App\Lead;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckUniqueEmail implements Rule
{
    protected $companyId;
    protected $message;
    protected $route;

    public function __construct($companyId=null, $route=null)
    {
        $this->companyId   = $companyId;
        $this->route   = $route;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $companyID = (!is_null($this->companyId)) ? $this->companyId : company()->id;
        $lead = Lead::withoutGlobalScopes([CompanyScope::class])->where('client_email', $value)->where('company_id', $companyID);
            if(!is_null($this->route) && $this->route != '')
            {
                $lead = $lead->where('id', '<>', $this->route);
            }
        $lead = $lead->first();
        if(is_null($lead)){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.emailAlreadyExist');
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function checkCustomMessage()
    {
//        if($this->message){
//            return $this->message;
//        }
//        elseif($this->paraValues){
//            return __('messages.check_equal_after_validation', ['date' => $this->paraValues]);
//        }else
//        {
//            return str_replace('_', ' ', __('messages.check_equal_after_validation', ['date' => request()->{$this->parameters}]));
//        }
    }

}