<?php

namespace App\Observers;

use App\Events\LeaveEvent;
use App\Leave;
use App\LeaveType;
use App\Services\Google;
use App\User;
use App\Notification;

class LeaveObserver
{

    /**
     * Handle the leave "saving" event.
     *
     * @param  \App\Leave  $leave
     * @return void
     */
    public function saving(Leave $leave)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $leave->company_id = company()->id;
            $leaveTypes = LeaveType::where('id', $leave->leave_type_id)->first();
             $leave->paid = $leaveTypes->paid;
        }
    }

    public function created(Leave $leave)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->duration == 'multiple') {
                if (session()->has('leaves_duration')) {
                    event(new LeaveEvent($leave, 'created', request()->multi_date));
                }
            } else {
                event(new LeaveEvent($leave, 'created'));
            }

            if (!is_null($leave->leave_date) && !is_null($leave->user) && !is_null($leave->user->calendar_module) && $leave->user->calendar_module->leave_status) {
                $leave->event_id = $this->googleCalendarEvent($leave);
                $leave->save();
            }
        }
    }

    public function updating(Leave $leave)
    {
        $leaveTypes = LeaveType::where('id', $leave->leave_type_id)->first();
        $leave->paid = $leaveTypes->paid;
        if (!is_null($leave->leave_date) && !is_null($leave->user) && !is_null($leave->user->calendar_module) && $leave->user->calendar_module->leave_status) {
            $leave->event_id = $this->googleCalendarEvent($leave);
        }
    }

    public function updated(Leave $leave)
    {
        if (!app()->runningInConsole()) {
            if ($leave->isDirty('status')) {
                event(new LeaveEvent($leave, 'statusUpdated'));
            } else {
                event(new LeaveEvent($leave, 'updated'));
            }
        }
    }

    public function deleting(Leave $leave)
    {
        $notifiData = ['App\Notifications\LeaveApplication', 'App\Notifications\LeaveStatusApprove','App\Notifications\LeaveStatusUpdate','App\Notifications\NewLeaveRequest','App\Notifications\NewMultipleLeaveRequest'];

        $notifications = Notification::
        whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('data', 'like', '{"id":'.$leave->id.',%')
            ->delete();
    }

    protected function googleCalendarEvent($leave)
    {
        if (company() && global_settings()->google_calendar_status == 'active') {

            $google = new Google();
            $company = company();
            $attendiesData = [];

            $attendees = User::where('id', $leave->user_id)->first();

            $attendiesData[] = ['email' => $attendees->email];

            $googleAccount = $company->googleAccount;
            if ($googleAccount) {

                $description = __('email.contract.subject');
                $description .= $attendees->name.' '. __('app.leave');

                // Create event
                $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $attendees->name,
                    'location' => ' ',
                    'description' => $description,
                    'start' => array(
                        'dateTime' => $leave->leave_date,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $leave->leave_date,
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
                    if ($leave->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $leave->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }

                    return $results->id;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            return $leave->event_id;
        }
    }

}
