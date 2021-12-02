<?php

namespace App\Observers;

use App\Contract;
use App\Notifications\NewContract;
use App\Services\Google;
use App\User;
use App\Notification;

class ContractObserver
{

    public function created(Contract $contract)
    {
        if (!isRunningInConsoleOrSeeding() && !is_null($contract->client)){
            $contract->client->notify(new NewContract($contract));
        }
    }

    public function updated(Contract $contract)
    {
        if (!isRunningInConsoleOrSeeding() && !is_null($contract->client)){
            $contract->client->notify(new NewContract($contract));
        }
    }

    public function saving(Contract $contract)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $contract->company_id = company()->id;
            if($contract && !is_null($contract->end_date))
            {
                $contract->event_id = $this->googleCalendarEvent($contract);
            }
        }
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
            if ($googleAccount) {

                $description = __('messages.invoiceDueOn');

                // Create event
                $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $invoice->contract_name,
                    'location' => $company->company_name,
                    'description' => '',
                    'start' => array(
                        'dateTime' => $invoice->end_date,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $invoice->end_date,
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

    public function deleting(Contract $contract)
    {
        $notifiData = ['App\Notifications\NewContract', 'App\Notifications\ContractComment', 'App\Notifications\ContractSigned'];

        $notifications = Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$contract->id.',%')
            ->delete();
    }

}
