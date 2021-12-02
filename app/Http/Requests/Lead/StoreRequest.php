<?php

namespace App\Http\Requests\Lead;

use App\Http\Requests\CoreRequest;
use App\Rules\CheckAfterDate;
use App\Rules\CheckUniqueEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreRequest extends CoreRequest
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
//        $this->merge(['client_email' => $this->email]);

        if($this->has('company_id')){
            $setting = \App\Company::findOrFail($this->company_id);
        }
        else{
            $setting = company();
        }

        $global = \App\GlobalSetting::first();

        $rules = [
            'name' => 'required',
            'email' => ['required','email',new CheckUniqueEmail($this->company_id, null)],
        ];

        if(!is_null($setting) && Route::currentRouteName() == 'front.leadStore')
        {
            if($global->google_captcha_version == 'v2' && $setting->lead_form_google_captcha){
                $rules['g-recaptcha-response'] = 'required';
            }

            if($global->google_captcha_version == 'v3' && $setting->lead_form_google_captcha){
                $rules['recaptcha_token'] = 'required';
            }
        }

        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');
            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\CustomField::findOrFail($id);
                if ($customField->required == 'yes' && (is_null($value) || $value == '')) {
                    $rules["custom_fields_data[$key]"] = 'required';
                }
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');
            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = \App\CustomField::findOrFail($id);
                if ($customField->required == 'yes') {
                    $attributes["custom_fields_data[$key]"] = $customField->label;
                }
            }
        }
        return $attributes;
    }

}
