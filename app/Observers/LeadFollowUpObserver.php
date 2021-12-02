<?php

namespace App\Observers;

use App\Company;
use App\Events\LeadEvent;
use App\GoogleAccount;
use App\Lead;
use App\LeadAgent;
use App\LeadFollowUp;
use App\Notifications\LeadAgentAssigned;
use App\Services\Google;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;

class LeadFollowUpObserver
{

    public function created(LeadFollowUp $followUp)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            if (!is_null($followUp->next_follow_up_date)) {
                $followUp->event_id = $this->googleCalendarEvent($followUp);
                $followUp->save();
            }
        }
    }

    public function updating(LeadFollowUp $followUp)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            if (!is_null($followUp->next_follow_up_date)) {
                $followUp->event_id = $this->googleCalendarEvent($followUp);
            }
        }
    }

    // Google calendar for single
    protected function googleCalendarEvent($event)
    {
        if (company() && global_settings()->google_calendar_status == 'active') {

            $google = new Google();
            $company = company();
            $attendiesData = [];

            $attendees = LeadAgent::with(['user','user.calendar_module'])->where('lead_id', $event->lead->id)->get();

            foreach($attendees as $attend){
                if(!is_null($attend->user) && !is_null($attend->user->email) && !is_null($attend->user->calendar_module) && $attend->user->calendar_module->lead_status)
                {
                    $attendiesData[] = ['email' => $attend->user->email];
                }
            }

            //            if($event->lead->email)
            //            {
            //                $attendiesData[] = ['email' => $event->lead->email];
            //            }

            $googleAccount = GoogleAccount::where('company_id', company()->id)->first();;
            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {

                // Create event
                $google = $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $event->remark,
                    'location' => '',
                    'description' => '',
                    'start' => array(
                        'dateTime' => $event->next_follow_up_date,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $event->next_follow_up_date,
                        'timeZone' => $company->timezone,
                    ),
                    'attendees' => $attendiesData,
                    'colorId' => 7,
                    'reminders' => array(
                        'useDefault' => false,
                        'overrides' => array(
                            array('method' => 'email', 'minutes' => 24 * 60),
                            array('method' => 'popup', 'minutes' => 10),
                        ),
                    ),
                ));

                try {
                    if ($event->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $event->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }

                    return $results->id;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            return $event->event_id;
        }
    }

}
