<?php

namespace App\Http\Controllers\Admin;

use App\Contract;
use App\ContractRenew;
use App\ContractSign;
use App\Helper\Reply;
use App\Http\Requests\Admin\Contract\RenewRequest;
use Carbon\Carbon;

class AdminContractRenewController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageIcon = 'fa fa-file';
        $this->pageTitle = 'contracts';
        $this->middleware(function ($request, $next) {
            if (!in_array('contracts', $this->user->modules)) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index($id)
    {
        $this->contract = Contract::find($id);
        return view('admin.contracts.renew.renew', $this->data);
    }

    public function renew(RenewRequest $request, $id)
    {
        $contract = Contract::find($id);

        $contractRenew = new ContractRenew();
        $contractRenew->amount = $request->amount_1;
        $contractRenew->renewed_by = $this->user->id;
        $contractRenew->contract_id = $id;
        $contractRenew->start_date = Carbon::createFromFormat($this->global->date_format, $request->start_date_1)->format('Y-m-d');
        $contractRenew->end_date = Carbon::createFromFormat($this->global->date_format, $request->end_date_1)->format('Y-m-d');
        $contractRenew->save();

        if (!$request->keep_customer_signature) {
            ContractSign::where('contract_id', $contract->id)->delete();
        }

        $contract->amount = $contractRenew->amount;
        $contract->start_date = $contractRenew->start_date;
        $contract->end_date = $contractRenew->end_date;
        $contract->save();

        return Reply::success('successfully renewed');
    }

    public function destroy($id)
    {
        $contractRenew = ContractRenew::find($id);

        $findNext = ContractRenew::whereRaw("created_at > '$contractRenew->created_at'")->first();

        if (!$findNext) {
            $findPrevious = ContractRenew::whereRaw("created_at < '$contractRenew->created_at'")->latest()->first();
            $contract = Contract::find($contractRenew->contract_id);
            if ($findPrevious) {
                $contract->start_date = $findPrevious->start_date;
                $contract->end_date = $findPrevious->end_date;
                $contract->amount = $findPrevious->amount;
                $contract->save();
            } else {
                $contract->start_date = $contract->original_start_date;
                $contract->end_date = $contract->original_end_date;
                $contract->amount = $contract->original_amount;
                $contract->save();
            }
        }

        ContractRenew::destroy($id);
        return Reply::success(__('messages.renewalDeleted'));
    }

}
