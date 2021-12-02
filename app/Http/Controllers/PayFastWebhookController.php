<?php

namespace App\Http\Controllers;

use App\Designation;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Middleware\VerifyCsrfToken;
use App\PayfastInvoice;
use App\Payment;

class PayFastWebhookController extends Controller
{

    public function saveInvoice(Request $request)
    {
        $pfParamString = '';
        // Tell PayFast that this page is reachable by triggering a header 200
        header( 'HTTP/1.0 200 OK' );
        flush();
        
        define( 'SANDBOX_MODE', true );

        $pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
        
        // Posted variables from ITN
        $pfData = $_POST;
    
        // Strip any slashes in data
        foreach( $pfData as $key => $val ) {
            $pfData[$key] = stripslashes( $val );
        }

        // Convert posted variables to a string
        foreach( $pfData as $key => $val ) {
            // \Log::debug($key);
            if( $key !== 'signature' ) {
                $pfParamString .= $key .'='. urlencode( $val ) .'&';
            } else {
                break;
            }
        }
        
        $cartTotal = number_format( sprintf( '%.2f', $request->amount ), 2, '.', '' );
        $pfParamString = substr( $pfParamString, 0, -1 );
        $passphrase = $request->passphrase;
    
        if($request->type == 'monthly'){
            $paydate = $pfData['billing_date'];
            $newDate = Carbon::createFromDate($paydate)->addMonth()->format('Y-m-d');
        } else {
            $paydate = $pfData['billing_date'];
            $newDate = Carbon::createFromDate($paydate)->addYear()->format('Y-m-d');
        }

        $check1 = $this->pfValidSignature($pfData, $pfParamString, $passphrase);
        $check2 = $this->pfValidIP();
        $check3 = $this->pfValidPaymentData($cartTotal, $pfData);
        $check4 = $this->pfValidServerConfirmation($pfParamString, $pfHost);

        if($check1 && $check2 && $check3 && $check4) {
            // if($request->notifyFor == 'admin'){

                $invoice = new PayfastInvoice();
                $invoice->company_id = $request->companyId;
                $invoice->package_id = $request->packageId;
                $invoice->m_payment_id = $pfData['m_payment_id'];
                $invoice->pf_payment_id = $pfData['pf_payment_id'];
                $invoice->payfast_plan = $request->planType;
                $invoice->amount = $pfData['amount_gross'];
                $invoice->pay_date = $pfData['billing_date'];
                $invoice->next_pay_date = carbon::parse($newDate)->format('Y-m-d');
                $invoice->signature = $pfData['signature'];
                $invoice->token = $pfData['token'];
                $invoice->status = $pfData['payment_status'];
                $invoice->save();

            // } elseif ($request->notifyFor == 'client') {
               
                // $payment = Payment::orderBy('id', 'DESC')->first();
                // $payment->company_id = $request->companyId;
                // $payment->project_id = $request->projectId;
                // $payment->invoice_id = $request->invoiceId;
                // $payment->amount = $pfData['amount_gross'];
                // $payment->paid_on = $pfData['billing_date'];
                // $payment->status = 'complete';
                // $payment->save();
            // }
        } else {
            // Some checks have failed, check payment manually and log for investigation
        }

    }

    public function pfValidSignature( $pfData, $pfParamString ,$passphrase)
    {
        // Calculate security signature
        if($passphrase === null) {
            $tempParamString = $pfParamString;
        } else {
            $tempParamString = $pfParamString.'&passphrase='.urlencode( $passphrase );
        }
    
        $signature = md5( $tempParamString );
        return ( $pfData['signature'] === $signature );
    }

    public function pfValidIP()
    {
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
            );
    
        $validIps = [];
    
        foreach( $validHosts as $pfHostname ) {
            $ips = gethostbynamel( $pfHostname );
    
            if( $ips !== false ) {
                $validIps = array_merge( $validIps, $ips );
            }
        }
    
        // Remove duplicates
        $validIps = array_unique( $validIps );
        $referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);
        if( in_array( $referrerIp, $validIps, true ) ) {
            return true;
        }
        return false;
    }
  
    public function pfValidPaymentData( $cartTotal, $pfData )
    {
        return !(abs((float)$cartTotal - (float)$pfData['amount_gross']) > 0.01);
    }

    public function pfValidServerConfirmation( $pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null )
    {

        // Use cURL (if available)
        if( in_array( 'curl', get_loaded_extensions(), true ) ) {
            // Variable initialization
            $url = 'https://'. $pfHost .'/eng/query/validate';
    
            // Create default cURL object
            $ch = curl_init();
        
            // Set cURL options - Use curl_setopt for greater PHP compatibility
            // Base settings
            curl_setopt( $ch, CURLOPT_USERAGENT, null );  // Set user agent
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
            curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
            
            // Standard settings
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
            if( !empty( $pfProxy ) ) {
                curl_setopt( $ch, CURLOPT_PROXY, $pfProxy );
            }
        
            // Execute cURL
            $response = curl_exec( $ch );
            curl_close( $ch );
            if ($response === 'VALID') {
                return true;
            }
        }
        return false;
    }
}