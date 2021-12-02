<?php

namespace App\Http\Controllers\Admin;

use App\ClientSubCategory;
use App\Currency;
use App\DataTables\Admin\PaymentsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Payments\ImportPayment;
use App\Http\Requests\Payments\StorePayment;
use App\Http\Requests\Payments\UpdatePayments;
use App\Invoice;
use App\Payment;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\Files;

class ManagePaymentsController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.payments';
        $this->pageIcon = 'fa fa-money';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('payments', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index(PaymentsDataTable $dataTable)
    {
        $this->projects = Project::all();
        return $dataTable->render('admin.payments.index', $this->data);
    }

    public function create()
    {
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->invoices = Invoice::where(function ($q) {
                $q->where('status', 'unpaid')
                    ->orWhere('status', 'partial')
                    ->orWhereRaw('(invoices.total - ((select IFNULL(sum(payments.amount),0) from payments where invoice_id = invoices.id) + (select IFNULL(sum(credit_notes_invoice.credit_amount), 0) from credit_notes_invoice where invoice_id = invoices.id)))  > 0');
        })
            ->get();

        if (request()->get('project')) {
            $this->projectId = request()->get('project');
        }
        return view('admin.payments.create', $this->data);
    }

    public function store(StorePayment $request)
    {
        $payment = new Payment();
        if(!is_null($request->currency_id)){
            $payment->currency_id = $request->currency_id;
        }
        else{
            $payment->currency_id = $this->global->currency_id;
        }
        if ($request->has('invoice_id') && $request->get('invoice_id') != '') {
            $invoice = Invoice::findOrFail($request->invoice_id);
            $payment->project_id = $invoice->project_id;
            $payment->invoice_id = $invoice->id;
            $payment->currency_id = $invoice->currency->id;
            
        } else if ($request->project_id != '') {
            $payment->project_id = $request->project_id;
        }

        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat($this->company->date_format.' '.$this->company->time_format, $request->paid_on)->format('Y-m-d H:i:s');

        $payment->remarks = $request->remarks;

        if ($request->hasFile('bill')) {
            $payment->bill = $request->bill->hashName();
            $request->bill->store('payment-receipt');
        }
        $payment->save();

        if ($request->has('invoice_id') && $request->get('invoice_id') != '') {
            $paidAmount = $invoice->amountPaid();
            if (($paidAmount + $request->amount) >= $invoice->total) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();
        }



        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        // change invoice status if exists
        if ($payment->invoice) {
            $due = $payment->invoice->amountDue() + $payment->amount;
            if ($due <= 0) {
                $payment->invoice->status = 'paid';
            } else if ($due >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            } else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        $payment->delete();

        return Reply::success(__('messages.paymentDeleted'));
    }

    public function edit($id)
    {
        $this->projects = Project::all();
        $this->currencies = Currency::all();
        $this->payment = Payment::findOrFail($id);
        $invoice = Invoice::find($this->payment->invoice_id);

        $invoices = Invoice::where(function ($q) {
            $q->where('status', 'unpaid')
                ->orWhere('status', 'partial')
                ->orWhereRaw('(invoices.total - ((select IFNULL(sum(payments.amount),0) from payments where invoice_id = invoices.id) + (select IFNULL(sum(credit_notes_invoice.credit_amount), 0) from credit_notes_invoice where invoice_id = invoices.id)))  > 0');
        });
        if($invoice)
        {
            $invoices = $invoices->where('id', '<>', $invoice->id);
        }
        $invoices = $invoices->get();
        $merged = ($invoice) ? $invoices->push($invoice) : $invoices;

        $this->invoices = $merged;
        return view('admin.payments.edit', $this->data);
    }

    public function update(UpdatePayments $request, $id)
    {
       $paymentTotal = Payment::selectRaw('SUM(amount) as  amount')->where('invoice_id',$request->invoice_id)->get();
       $inoviceTotal = Invoice::select('total')->where('id',$request->invoice_id)->get();

       if(!is_null($inoviceTotal) && !is_null($paymentTotal)){ 
            if($paymentTotal[0]->amount >= $inoviceTotal[0]->total && $request->status == 'pending'){
                return Reply::error(__('messages.payemnentAmount'));

            } 
        }
        $payment = Payment::findOrFail($id);
        if ($request->project_id != '') {
            $payment->project_id = $request->project_id;
        }
        $payment->currency_id = $request->currency_id;
        $payment->invoice_id = $request->invoice_id;
        $payment->amount = round($request->amount, 2);
        $payment->gateway = $request->gateway;
        $payment->transaction_id = $request->transaction_id;
        $payment->paid_on = Carbon::createFromFormat($this->company->date_format.' '.$this->company->time_format, $request->paid_on)->format('Y-m-d H:i:s');
        $payment->status = $request->status;
        $payment->remarks = $request->remarks;
        if ($request->hasFile('bill')) {
            Files::deleteFile($payment->bill, 'payment-receipt');
            $payment->bill = $request->bill->hashName();
            $request->bill->store('payment-receipt');
        }
        $payment->save();

        // change invoice status if exists
        if ($payment->invoice) {
            if ($payment->invoice->amountDue() <= 0) {
                $payment->invoice->status = 'paid';
            } else if ($payment->invoice->amountDue() >= $payment->invoice->total) {
                $payment->invoice->status = 'unpaid';
            } else {
                $payment->invoice->status = 'partial';
            }
            $payment->invoice->save();
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.paymentSuccess'));
    }

    public function payInvoice($invoiceId)
    {
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->paidAmount = $this->invoice->amountPaid();


        if ($this->invoice->status == 'paid') {
            return 'Invoice already paid';
        }

        return view('admin.payments.pay-invoice', $this->data);
    }

    public function importExcel(ImportPayment $request)
    {
        if ($request->hasFile('import_file')) {
            $path = $request->file('import_file')->getRealPath();
            $data = Excel::load($path)->get();

            if ($data->count()) {

                foreach ($data as $key => $value) {

                    if ($request->currency_character) {
                        $amount = substr($value->amount, 1);
                    } else {
                        $amount = substr($value->amount, 0);
                    }

                    $amount = str_replace(',', '', $amount);
                    $amount = str_replace(' ', '', $amount);

                    $arr[] = [
                        'paid_on' => Carbon::parse($value->date)->format('Y-m-d'),
                        'amount' => $amount,
                        'currency_id' => $this->global->currency_id,
                        'status' => 'complete'
                    ];
                }

                if (!empty($arr)) {
                    DB::table('payments')->insert($arr);
                }
            }
        }

        return Reply::redirect(route('admin.payments.index'), __('messages.importSuccess'));
    }

    public function downloadSample()
    {
        return response()->download(public_path() . '/sample/payment-sample.csv');
    }

    public function show($id)
    {
        $this->payment = Payment::with('invoice', 'project', 'currency')->find($id);
        return view('admin.payments.show', $this->data);
    }

    public function invoiceByProject(Request $request)
    {
        $invoices = Invoice::where(function ($q) {
            $q->where('status', 'unpaid')
                ->orWhere('status', 'partial');
        });
        if($request->project_id){
            $invoices = $invoices->where('project_id', $request->project_id);
        }
        $invoices = $invoices->get();
        $option = '';

        if(sizeof($invoices) > 0){
            $option = '<option value="">--</option>';
        }
        foreach($invoices as $invoice){
            $option .= '<option value="'.$invoice->id.'">'.$invoice->invoice_number.'</option>';
        }

        return Reply::dataOnly(['invoices' => $option]);
    }

}
