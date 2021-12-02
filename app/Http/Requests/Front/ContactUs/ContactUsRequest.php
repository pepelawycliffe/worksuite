<?php

namespace App\Http\Requests\Front\ContactUs;

use App\Http\Requests\CoreRequest;
use App\GlobalSetting;

class ContactUsRequest extends CoreRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $global = GlobalSetting::first();
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];
        if ($global->google_recaptcha_status && $global->google_captcha_version == 'v2') {
            $rules['g-recaptcha-response'] = 'required';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'Please select google recaptcha'
        ];
    }

}
