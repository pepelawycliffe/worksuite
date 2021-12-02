<?php

namespace App\Http\Controllers\Client;

use App\ClientPayment;
use App\Country;
use App\CreditNotes;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Invoices\OfflinePaymentRequest;
use App\Invoice;
use App\InvoiceItems;
use App\InvoiceSetting;
use App\Notifications\OfflineInvoicePaymentRequest;
use App\OfflineInvoicePayment;
use App\OfflinePaymentMethod;
use App\Payment;
use App\PaymentGatewayCredentials;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Stripe\Stripe;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ClientInvoicesController extends ClientBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.invoices';
        $this->pageIcon = 'ti-receipt';

        $this->middleware(function ($request, $next) {
            abort_if(!in_array('invoices', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
        return view('client.invoices.index', $this->data);
    }

    public function create()
    {
        $invoices = Invoice::leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->select('invoices.id', 'projects.project_name', 'invoices.invoice_number', 'currencies.currency_symbol', 'currencies.currency_code', 'invoices.total', 'invoices.issue_date', 'invoices.status')
            ->where(function ($query) {
                $query->where('projects.client_id', $this->user->id)
                    ->orWhere('invoices.client_id', $this->user->id);
            })
            ->where('invoices.status', '<>', 'draft')
            ->where('invoices.send_status', 1)
            ->where('invoices.status', '!=', 'canceled');

        return DataTables::of($invoices)
            ->addColumn('action', function ($row) {
                return '<a href="' . route('client.invoices.download', $row->id) . '" data-toggle="tooltip" data-original-title="Download" class="btn  btn-sm btn-outline btn-info"><i class="fa fa-download"></i> '.__('app.download').'</a>';
            })
            ->editColumn('project_name', function ($row) {
                return $row->project_name != '' ? $row->project_name : '--';
            })
            ->editColumn('invoice_number', function ($row) {
                return '<a style="text-decoration: underline" href="' . route('client.invoices.show', $row->id) . '">' . $row->invoice_number . '</a>';
            })
            ->editColumn('currency_symbol', function ($row) {
                return $row->currency_symbol . ' (' . $row->currency_code . ')';
            })
            ->editColumn('total', function ($row) {
                return currency_formatter($row->total, '');
            })
            ->editColumn('issue_date', function ($row) {
                return $row->issue_date->format($this->global->date_format);
            })
            ->editColumn('status', function ($row) {
                if ($row->status == 'unpaid') {
                    return '<label class="label label-danger">' .  __('app.' . $row->status) . '</label>';
                } else if($row->status == 'review') {
                    return '<label class="label label-warning">' . __('app.' . $row->status)  . '</label>';
                } else {
                    return '<label class="label label-success">' . __('app.' . $row->status) . '</label>';
                }
            })
            ->rawColumns(['action', 'status', 'invoice_number'])
            ->removeColumn('currency_code')
            ->make(true);
    }

    public function download($id)
    {

        $this->invoice = Invoice::select('invoices.*')->leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->where(function ($query) {
                $query->where('projects.client_id', $this->user->id)
                    ->orWhere('invoices.client_id', $this->user->id);
            })->where('invoices.id', $id)->where('credit_note', 0)->findOrFail($id)->withCustomFields();

        $this->paidAmount = $this->invoice->getPaidAmount();
        $this->creditNote = 0;

        if ($this->invoice->credit_note) {
            $this->creditNote = CreditNotes::where('invoice_id', $id)
                ->select('cn_number')
                ->first();
        }

        // Download file uploaded
        if ($this->invoice->file != null) {
            return download_local_s3($this->invoice, 'invoice-files/' . $this->invoice->file);
        }

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }


        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();


        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;

        $this->invoiceSetting = InvoiceSetting::first();

        $this->company = $this->invoice->company;
        $pdf = app('dompdf.wrapper');
        $this->payments = Payment::with(['offlineMethod'])->where('invoice_id', $this->invoice->id)->where('status', 'complete')->orderBy('paid_on', 'desc')->get();
        $this->fields = $this->invoice->getCustomFieldGroupsWithFields()->fields;

        $pdf->getDomPDF()->set_option('enable_php', true);
        App::setLocale($this->invoiceSetting->locale);
        Carbon::setLocale($this->invoiceSetting->locale);
        $pdf->loadView('invoices.' . $this->invoiceSetting->template, $this->data);

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $canvas->page_text(530, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', null, 10, array(0, 0, 0));

        $filename = $this->invoice->invoice_number;

        return $pdf->download($filename . '.pdf');
    }

    public function show($id)
    {
        $this->invoice = Invoice::with('offline_invoice_payment', 'offline_invoice_payment.payment_method')
            ->select('invoices.*')
            ->leftJoin('projects', 'projects.id', '=', 'invoices.project_id')
            ->where(function ($query) {
                $query->where('projects.client_id', $this->user->id)
                    ->orWhere('invoices.client_id', $this->user->id);
            })
            ->where('credit_note', 0)
            ->findOrFail($id);
        
        $this->paidAmount = $this->invoice->getPaidAmount();

        if ($this->invoice->discount > 0) {
            if ($this->invoice->discount_type == 'percent') {
                $this->discount = (($this->invoice->discount / 100) * $this->invoice->sub_total);
            } else {
                $this->discount = $this->invoice->discount;
            }
        } else {
            $this->discount = 0;
        }

        $taxList = array();

        $items = InvoiceItems::whereNotNull('taxes')
            ->where('invoice_id', $this->invoice->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax){
                $this->tax = InvoiceItems::taxbyid($tax)->first();
                if ($this->tax){
                    if (!isset($taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'])) {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = ($this->tax->rate_percent / 100) * $item->amount;
                    } else {
                        $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] = $taxList[$this->tax->tax_name . ': ' . $this->tax->rate_percent . '%'] + (($this->tax->rate_percent / 100) * $item->amount);
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $this->settings = $this->global;
        $this->credentials = PaymentGatewayCredentials::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->invoice->company_id)
            ->first();

        $this->methods = OfflinePaymentMethod::activeMethod();

        $this->invoiceSetting = InvoiceSetting::first();
        $this->countries = Country::all();
        $this->payFastHtml = $this->payFastPayment($this->credentials, $this->invoice);

        return view('client.invoices.show', $this->data);
    }

    public function payFastPayment($credentials, $invoice)
    {
        $randomString = Str::random(30);
        $amount = $invoice->total;
        $projectId = $invoice->project_id;
        $invoiceId = $invoice->id;
        $companyId = $invoice->company_id;

        $payment = Payment::where('invoice_id', $invoiceId)->first();
       
        if($payment){
            $paymentId = $payment->id;
        } else {
            $invoicePayment = new Payment();
            $invoicePayment->company_id = $companyId;
            $invoicePayment->project_id = $projectId;
            $invoicePayment->invoice_id = $invoiceId;
            $invoicePayment->amount = $amount;
            $invoicePayment->status  = 'pending';
            $invoicePayment->save();
            $paymentId = $invoicePayment->id;
        }
      
        $item = InvoiceItems::where('invoice_id', $invoice->id)
        ->first();
       
        $passphrase = $credentials->payfast_salt_passphrase;

        // Construct variables
        $cartTotal = number_format( sprintf( '%.2f', $amount ), 2, '.', '' );// This amount needs to be sourced from your application
        $data = array(
            // Merchant details
            'merchant_id' => $credentials->payfast_key,
            'merchant_key' => $credentials->payfast_secret,
            'return_url' => route('client.invoices.payfast-success', compact('invoiceId')),
            'cancel_url' => route('client.invoices.payfast-cancel', compact('invoiceId')),
            'notify_url' => route('client-payfast-invoice', compact('amount', 'passphrase', 'paymentId')),
            // Buyer details
            'name_first' => user()->name,
            'email_address' => user()->email,
            // Transaction details
            'm_payment_id' => $randomString, //Unique payment ID to pass through to notify_url
            'amount' => $cartTotal,
            'item_name' => $item->item_name,
        );
      
        $signature = $this->generateSignature($data, $credentials->payfast_salt_passphrase);
        
        $data['signature'] = $signature;

        if($credentials->payfast_mode == 'sandbox'){
            $environment = 'https://sandbox.payfast.co.za/eng/process';
        } else {
            $environment = 'https://www.payfast.co.za/eng/process';
        }

        $htmlForm = '<form action="'.$environment.'" method="post">';
        foreach($data as $name => $value)
        {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
        }
        $htmlForm .= '<button type="submit" class="btn btn-default payFastPayment bg-white" style="border:none;" data-toggle="tooltip" data-placement="top">
                    <span style="margin-left:8px;">
                    <img height="15px" id="company-logo-img" src="'.asset('img/payFast-coins.png').'">'.' '.__('modules.invoices.payPayFast').'</span></button>
                    </form>';

        return $htmlForm;
    }

    public function generateSignature($data, $passPhrase = null)
    {
        // Create parameter string
        $pfOutput = '';
        foreach( $data as $key => $val ) {
            if($val !== '') {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }
        }
        // Remove last ampersand
        $getString = substr( $pfOutput, 0, -1 );
        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }
        return md5( $getString );
    }

    public function payfastSuccess(Request $request)
    {
        try{
            $id = $request->invoiceId;

            $invoice = Invoice::findOrFail($id);
            $invoice->status = 'paid';
            $invoice->save();

            \Session::flash('message', __('messages.paymentSuccessfullyDone'));
            return Redirect::route('client.invoices.show', $id);

        } catch(\Exception $e) {

            \Session::flash('message', __('messages.paymentFailed'));
            return Redirect::route('client.invoices.show', $id);
        }
       
    }

    public function payfastCancel(Request $request)
    {
        $id = $request->invoiceId;
        \Session::flash('message', __('messages.paymentFailed'));
        return Redirect::route('client.invoices.show', $id);
    }

    public function stripeModal(Request $request)
    {
        $id = $request->invoice_id;
        $this->invoice = Invoice::with('offline_invoice_payment', 'offline_invoice_payment.payment_method')->where([
            'id' => $id,
        //            'credit_note' => 0
        ])
            ->whereHas('project', function ($q) {
                $q->where('client_id', $this->user->id);
            }, '>=', 0)
            ->firstOrFail();

        $this->settings = $this->global;
        $this->credentials = PaymentGatewayCredentials::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->invoice->company_id)
            ->first();

        if($this->credentials->stripe_secret)
        {
            Stripe::setApiKey($this->credentials->stripe_secret);

            $total = $this->invoice->total;
            $totalAmount = $total;

            $customer = \Stripe\Customer::create([
                'email' => $this->invoice->client_id ? $this->invoice->client->email : $this->invoice->project->clientdetails->email,
                'name' => $request->clientName,
                'address' => [
                    'line1' => $request->clientName,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                ],
            ]);

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $totalAmount * 100,
                'currency' => $this->invoice->currency->currency_code,
                'customer' => $customer->id,
                'setup_future_usage' => 'off_session',
                'payment_method_types' => ['card'],
                'description' => $this->invoice->invoice_number. ' Payment',
                'metadata' => ['integration_check' => 'accept_a_payment', 'invoice_id' => $id]
            ]);

            $this->intent = $intent;
        }
        $customerDetail = [
            'email' => $this->invoice->client_id ? $this->invoice->client->email : $this->invoice->project->clientdetails->email,
            'name' => $request->clientName,
            'line1' => $request->clientName,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ];

        $this->customerDetail = $customerDetail;


        $view = view('client.invoices.stripe-payment', $this->data)->render();

        return Reply::dataOnly(['view' => $view]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $invoiceId = $request->invoiceId;
        $invoice   = Invoice::findOrFail($invoiceId);

        $clientPayment = new ClientPayment();
        $clientPayment->currency_id       = $invoice->currency_id;
        $clientPayment->company_id        = $invoice->company_id;
        $clientPayment->invoice_id        = $invoice->id;
        $clientPayment->project_id        = $invoice->project_id;
        $clientPayment->amount            = $invoice->total;
        $clientPayment->offline_method_id = $request->offlineId;
        $clientPayment->transaction_id    = Carbon::now()->timestamp;
        $clientPayment->gateway           = 'Offline';
        $clientPayment->status            = 'complete';
        $clientPayment->paid_on           = Carbon::now();
        $clientPayment->save();

        $invoice->status = 'paid';
        $invoice->save();

        return Reply::redirect(route('client.invoices.show', $invoiceId), __('messages.paymentSuccess'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function offlinePayment(Request $request)
    {
        $this->offlineId = $request->offlineId;
        $this->invoiceId = $request->invoiceId;

        return \view('client.invoices.offline-payment', $this->data);
    }

    public function offlinePaymentSubmit(OfflinePaymentRequest $request)
    {
        $checkAlreadyRequest = Invoice::with(['offline_invoice_payment' => function($q) {
            $q->where('status', 'pending');
        }])->where('id', $request->invoice_id)->first();

        if($checkAlreadyRequest->offline_invoice_payment->count() > 0 ) {
            return Reply::error('You have already raised a request.');
        }

        $checkAlreadyRequest->status = 'review';
        $checkAlreadyRequest->save();

        // create offline payment request
        $offlinePayment = new OfflineInvoicePayment();
        $offlinePayment->invoice_id        = $checkAlreadyRequest->id;
        $offlinePayment->client_id         = $this->user->id;
        $offlinePayment->payment_method_id = $request->offline_id;
        $offlinePayment->description       = $request->description;


        if ($request->hasFile('slip')) {
            $offlinePayment->slip = Files::upload($request->slip, 'offline-payment-files', null, null, false);
        }

        $offlinePayment->save();

        $clientPayment = new ClientPayment();
        $clientPayment->currency_id       = $checkAlreadyRequest->currency_id;
        $clientPayment->company_id        = $checkAlreadyRequest->company_id;
        $clientPayment->invoice_id        = $checkAlreadyRequest->id;
        $clientPayment->project_id        = $checkAlreadyRequest->project_id;
        $clientPayment->amount            = $checkAlreadyRequest->total;
        $clientPayment->offline_method_id = $request->offline_id;
        $clientPayment->transaction_id    = Carbon::now()->timestamp;
        $clientPayment->gateway           = 'Offline';
        $clientPayment->status            = 'pending';
        $clientPayment->paid_on           = Carbon::now();
        $clientPayment->save();

        return Reply::redirect(route('client.invoices.show', $checkAlreadyRequest->id));

    }

}
