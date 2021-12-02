<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\Tax\StoreTax;
use App\ProductCategory;
use App\Tax;

class TaxSettingsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        $this->taxes = Tax::all();
        return view('admin.taxes.create', $this->data);
    }

    public function store(StoreTax $request)
    {
        $tax_name_array = !is_null($request->tax_name_array) ? explode(',', $request->tax_name_array) : null;
        $tax = new Tax();
        $tax->tax_name = $request->tax_name;
        $tax->rate_percent = $request->rate_percent;
        $tax->save();
        $allTax = Tax::all();
        $tax = '';
        foreach ($allTax as $key => $value) {
            $selected = (!is_null($tax_name_array) && in_array($value->id, $tax_name_array)) ? 'selected' : '';
            $tax .= '<option '.$selected.' data-rate=" '.$value->rate_percent.'" data-content="' . $value->tax_name .':'. $value->rate_percent .'%' . '" value="' . $value->id . '">'.$value->tax_name.':'.$value->rate_percent.'%'.'</option>';
        }
        return Reply::successWithData(__('messages.taxAdded'), ['tax' => $tax,'data' => $this->data]);
        //  return Reply::success(__('messages.taxAdded'));
    }

    public function destroy($id)
    {
        Tax::destroy($id);
        return Reply::success(__('messages.taxDelete'));
    }

}
