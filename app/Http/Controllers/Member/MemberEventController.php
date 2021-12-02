<?php

namespace App\Http\Controllers\Member;

use App\Event;
use App\EventAttendee;
use App\GoogleAccount;
use App\Helper\Reply;
use App\Http\Requests\Events\StoreEvent;
use App\Http\Requests\Events\UpdateEvent;
use App\Notifications\EventInvite;
use App\Services\Google;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MemberEventController extends MemberBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.Events';
        $this->pageIcon = 'icon-calender';
        $this->middleware(function ($request, $next) {
            if(!in_array('events', $this->user->modules)){
                abort(403);
            }
            return $next($request);
        });

    }

    public function index()
    {
        if($this->user->cans('view_events')){
            $this->events = Event::all();
        }
        else{
            $this->events = Event::join('event_attendees', 'event_attendees.event_id', '=', 'events.id')
                ->where('event_attendees.user_id', $this->user->id)
                ->select('events.*')
                ->get();
        }
        $this->employees = User::all();

        return view('member.event-calendar.index', $this->data);
    }

    public function show($id)
    {
        $this->event = Event::findOrFail($id);
        $this->startDate = explode(' ', request('start'));
        $this->startDate = Carbon::parse($this->startDate[0]);

        return view('member.event-calendar.show', $this->data);
    }

    public function store(StoreEvent $request)
    {
        $event = new Event();
        $event->event_name = $request->event_name;
        $event->where = $request->where;
        $event->description = $request->description;
        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d').' '.Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d').' '.Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');

        if($request->repeat){
            $event->repeat = $request->repeat;
        }
        else{
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        $event->save();

        $event->event_id = $this->googleCalendarEvent($event);
        $event->save();


        if($request->all_employees){
            $attendees = User::allEmployees();
            foreach($attendees as $attendee){
                EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
            }

            Notification::send($attendees, new EventInvite($event));
        }

        if($request->user_id){
            foreach($request->user_id as $userId){
                EventAttendee::firstOrCreate(['user_id' => $userId, 'event_id' => $event->id]);
            }
            $attendees = User::whereIn('id', $request->user_id)->get();
            Notification::send($attendees, new EventInvite($event));
        }

        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function update(UpdateEvent $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->event_name = $request->event_name;
        $event->where = $request->where;
        $event->description = $request->description;
        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d').' '.Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d').' '.Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');

        if($request->repeat){
            $event->repeat = $request->repeat;
        }
        else{
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        $event->save();

        $event->event_id = $this->googleCalendarEvent($event);
        $event->save();

        if($request->all_employees){
            $attendees = User::allEmployees();
            foreach($attendees as $attendee){
                $checkExists = EventAttendee::where('user_id', $attendee->id)->where('event_id', $event->id)->first();
                if(!$checkExists){
                    EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($attendee->id);
                    $notifyUser->notify(new EventInvite($event));

                }
            }
        }

        if($request->user_id){
            foreach($request->user_id as $userId){
                $checkExists = EventAttendee::where('user_id', $userId)->where('event_id', $event->id)->first();
                if(!$checkExists){
                    EventAttendee::create(['user_id' => $userId, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($userId);
                    $notifyUser->notify(new EventInvite($event));
                }
            }
        }

        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function removeAttendee(Request $request)
    {
        EventAttendee::destroy($request->attendeeId);
        return Reply::dataOnly(['status' => 'success']);
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return Reply::success(__('messages.eventDeleteSuccess'));
    }

    public function edit($id)
    {
        if($this->user->cans('edit_events')){
            $this->employees = User::doesntHave('attendee', 'and', function($query) use ($id){
                $query->where('event_id', $id);
            })
                ->select('users.id', 'users.name', 'users.email', 'users.created_at')
                ->get();
            $this->event = Event::findOrFail($id);
            $view = view('member.event-calendar.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'view' => $view]);
        }
        return abort(403);
    }

    protected function googleCalendarEvent($event)
    {

        if (company() && global_settings()->google_calendar_status == 'active') {
            $google = new Google();
            $company = company();
            $attendiesData = [];

            $attendees = EventAttendee::with(['user'])->where('event_id', $event->id)->get();

            foreach($attendees as $attend){
                if(!is_null($attend->user) && !is_null($attend->user->email) && !is_null($attend->user->calendar_module) && $attend->user->calendar_module->event_status)
                {
                    $attendiesData[] = ['email' => $attend->user->email];
                }
            }

            $googleAccount = GoogleAccount::where('company_id', company()->id)->first();;
            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {

                $description = __('email.newEvent.subject');
                $description = $event->event_name . ' :- ' . $description;
                $description = $event->event_name . ' :- ' . $description . ' ' . $event->description;

                // Create event
                $google = $google->connectUsing($googleAccount->token);

                $eventData = new \Google_Service_Calendar_Event(array(
                    'summary' => $event->event_name,
                    'location' => $event->where,
                    'description' => $description,
                    'start' => array(
                        'dateTime' => $event->start_date_time,
                        'timeZone' => $company->timezone,
                    ),
                    'end' => array(
                        'dateTime' => $event->end_date_time,
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
