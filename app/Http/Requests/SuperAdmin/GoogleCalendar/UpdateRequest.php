<?php

namespace App\Http\Requests\SuperAdmin\GoogleCalendar;

use App\Http\Requests\SuperAdmin\SuperAdminBaseRequest;

class UpdateRequest extends SuperAdminBaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if($this->google_calendar_status == 'active'){
            $rules = [
                'google_client_id' => 'required',
                'google_client_secret'  => 'required',
            ];
        }
        return $rules;

    }

}
