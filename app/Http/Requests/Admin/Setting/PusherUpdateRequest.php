<?php

namespace App\Http\Requests\Admin\Setting;

use App\Http\Requests\CoreRequest;
use Illuminate\Foundation\Http\FormRequest;

class PusherUpdateRequest extends CoreRequest
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
        if($this->has('status')){
            return [
                'pusher_app_id' => 'required',
                'pusher_app_key' => 'required',
                'pusher_app_secret' => 'required',
                'pusher_cluster' => 'required'
            ];
        }
        return [];

    }

}
