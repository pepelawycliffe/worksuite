<?php

namespace App\Http\Controllers\SuperAdmin;

use App\GlobalSetting;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\signUpSetting\StoreRequest;
use App\LanguageSetting;
use App\SignUpSetting;

class SuperAdminSignUpController extends  SuperAdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Sign Up Setting';
        $this->pageIcon = 'icon-settings';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->registrationMessage = SignUpSetting::first();
        $this->activeLanguages = LanguageSetting::where('status', 'enabled')->orderBy('language_name', 'asc')->get();
        $this->registrationStatus = GlobalSetting::first();
      
        return view('super-admin.sign-up-setting.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function changeForm(Request $request)
    {
        $headerData = SignUpSetting::where('language_setting_id', $request->language_settings_id == 0 ? null : $request->language_settings_id)->first();
        if (empty($headerData)) {
            $view = view('super-admin.sign-up-setting.new-form', ['languageId' => $request->language_settings_id])->render();
        } else {
            $view = view('super-admin.sign-up-setting.edit-form', ['registrationMessage' => $headerData])->render();
        }
        
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $registration = SignUpSetting::where('language_setting_id', $request->language_settings_id == 0 ? null : $request->language_settings_id)->first();
       
        GlobalSetting::where('id', $request->id)->update(['enable_register' => $request->enable_register]);
        if (!is_null($registration)) {
            $registration->language_setting_id = $request->language_settings_id;
            $registration->message = $request->message;
            $registration->save();
        } else {
            $signUp = new SignUpSetting();
            $signUp->language_setting_id = $request->language_settings_id;
            $signUp->message = $request->message;
            $signUp->save();
        }

        return Reply::success('messages.updatedSuccessfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $registration = GlobalSetting::findOrFail($id);
        if(isset($request->status)){
            $registration->registration_open = $request->status;
        }else{
            $registration->enable_register = $request->enable_register;
        }
        $registration->save();

        return Reply::success(__('messages.updateSuccess'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
