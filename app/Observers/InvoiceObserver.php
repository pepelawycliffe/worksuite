<?php

namespace App\Observers;

use App\Estimate;
use App\Events\InvoicePaymentEvent;
use App\Events\LeadEvent;
use App\Invoice;
use App\Notification as Notificat;
use App\InvoiceItems;
use App\Notifications\ClientPurchaseInvoice;
use App\Notifications\InvoicePaymentReceived;
use App\Notifications\NewInvoice;
use App\Scopes\CompanyScope;
use App\Services\Google;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class InvoiceObserver
{

    public function creating(Invoice $invoice)
    {
        if ((request()->type && request()->type == 'send') || !is_null($invoice->invoice_recurring_id) || request()->client_product_invoice) {
            $invoice->send_status = 1;
        } else {
            $invoice->send_status = 0;
        }

        if (request()->type && request()->type == 'draft') {
            $invoice->status = 'draft';
        }

        if (request()->total && request()->total == 0) {
            $invoice->status = 'paid';
        }

        if (!is_null($invoice->estimate_id)) {
            $estimate = Estimate::findOrFail($invoice->estimate_id);
            if($estimate->status == 'accepted'){
                $invoice->send_status = 1;
            }
        }
    }

    public function saving(Invoice $invoice)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $invoice->company_id = company()->id;
        }
    }

    public function created(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if (!empty(request()->item_name)) {

                $itemsSummary = request()->input('item_summary');
                $cost_per_item = request()->input('cost_per_item');
                $quantity = request()->input('quantity');
                $hsnSacCode = request()->input('hsn_sac_code');
                $amount = request()->input('amount');
                $tax = request()->input('taxes');

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        InvoiceItems::create(
                            [
                                'invoice_id' => $invoice->id,
                                'item_name' => $item,
                                'hsn_sac_code' => (isset($hsnSacCode[$key]) && !is_null($hsnSacCode[$key])) ? $hsnSacCode[$key] : null,
                                'item_summary' => (isset($itemsSummary[$key]) && !is_null($itemsSummary[$key])) ? $itemsSummary[$key] : '',
                                'type' => 'item',
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                                'taxes' => $tax ? array_key_exists($key, $tax) ? json_encode($tax[$key]) : null : null
                            ]
                        );
                    }
                endforeach;
            }


            if (request()->type && request()->type == 'send') {

                if (($invoice->project && $invoice->project->client_id != null) || $invoice->client_id != null) {
                    $clientId = ($invoice->project && $invoice->project->client_id != null) ? $invoice->project->client_id : $invoice->client_id;
                    // Notify client
                    $notifyUser = User::withoutGlobalScopes([CompanyScope::class, 'active'])->findOrFail($clientId);

                    $notifyUser->notify(new NewInvoice($invoice));

                    if (!is_null($invoice->due_date)) {
                        $invoice->event_id = $this->googleCalendarEvent($invoice);
                        $invoice->save();
                    }
                }
            }
            if (request()->client_product_invoice){
                $admins = User::frontAllAdmins($invoice->company_id);
                Notification::send($admins, new ClientPurchaseInvoice($invoice));
            }
        }
    }

    public function updated(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if ($invoice->isDirty('status') && in_array($invoice->status, ['paid', 'partial']) && $invoice->credit_note != 1) {
                $admins = User::frontAllAdmins($invoice->company_id);
                event(new InvoicePaymentEvent($invoice, $admins));

               // Notification::send($admins, new InvoicePaymentReceived($invoice));
            }
        }
    }

    public function updating(Invoice $invoice)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if($invoice && !is_null($invoice->due_date))
            {
                $invoice->event_id = $this->googleCalendarEvent($invoice);
            }
        }
    }

    public function deleting(Invoice $invoice)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $invoice->id)->where('module_type', 'invoice')->get();
        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }
        $notifiData = ['App\Notifications\InvoicePaymentReceived', 'App\Notifications\InvoiceReminder','App\Notifications\NewInvoice','App\Notifications\NewPayment','App\Notifications\ClientPurchaseInvoice'];

        $notifications = Notificat::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$invoice->id.',%')
            ->delete();
    }

    protected function googleCalendarEvent($invoice)
    {
        if (company() && global_settings()->google_calendar_status == 'active') {

            $google = new Google();
            $company = company();
            $attendiesData = [];

            $attendees = User::where('id', $invoice->client_id)->first();
            if (!is_null($invoice->due_date) && !is_null($attendees) && !is_null($attendees->calendar_module) && $attendees->calendar_module->invoice_status) {
                $attendiesData[] = ['email' => $attendees->email];
            }

            $googleAccount = $company->googleAccount;
            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {

                $description = __('messages.invoiceDueOn');

                // Create event
                $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $invoice->invoice_number.' '.$description,
                    'location' => $company->company_name,
                    'description' => $description,
                    'start' => array(
                        'dateTime' => $invoice->start_date,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $invoice->due_date,
                        'timeZone' => $company->timezone,
                    ),
                    'attendees' => $attendiesData,
                    'reminders' => array(
                        'useDefault' => false,
                        'overrides' => array(
                            array('method' => 'email', 'minutes' => 24 * 60),
                            array('method' => 'popup', 'minutes' => 10),
                        ),
                    ),
                ));

                try {
                    if ($invoice->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $invoice->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }

                    return $results->id;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            return $invoice->event_id;
        }
    }

}
