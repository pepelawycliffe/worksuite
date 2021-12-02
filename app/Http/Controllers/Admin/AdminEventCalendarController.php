<?php

namespace App\Http\Controllers\Admin;

use App\Event;
use App\EventAttendee;
use App\GoogleAccount;
use App\Helper\Reply;
use App\Http\Requests\Events\StoreEvent;
use App\Http\Requests\Events\UpdateEvent;
use App\Notifications\EventInvite;
use App\Services\Google;
use App\User;
use App\EventCategory;
use App\EventType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminEventCalendarController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.Events';
        $this->pageIcon = 'icon-calender';
        $this->middleware(function ($request, $next) {
            abort_if(!in_array('events', $this->user->modules), 403);
            return $next($request);
        });
    }

    public function index()
    {
       
        $this->employees = User::allEmployees();
        $this->events = Event::all();
        $this->clients = User::allClients();
        $this->categories = EventCategory::all();
        $this->eventTypes = EventType::all();
        $this->unique_id = uniqid();
        return view('admin.event-calendar.index', $this->data);
    }

    public function filterEvent(Request $request)
    {
        $eventFilter = Event::select('events.id', 'event_name', 'label_color', 'where', 'description', 'start_date_time',
            'end_date_time', 'repeat', 'repeat_every', 'repeat_cycles', 'repeat_type', 'created_at', 'updated_at');
        if (request()->has('employee') && $request->employee != 0) {
            $eventFilter->whereHas('attendee', function ($query)use($request) {
                return $query->where('user_id', $request->employee);
                   
            });
        }
        if (request()->has('client') && $request->client != 0) {
            $eventFilter->whereHas('attendee', function ($query)use($request) {
                return $query->where('user_id', $request->client);
                   
            });
        }
        if (request()->has('category') && $request->category != 0) {
            $eventFilter->whereHas('category', function ($query)use($request) {
                return $query->where('category_id', $request->category);
                   
            });
        }
        if (request()->has('event_type') && $request->event_type != 0) {

            $eventFilter->whereHas('eventType', function ($query)use($request) {
                return $query->where('event_type_id', $request->event_type);
                   
            });
        }
            $eventFilter = $eventFilter->get();

            $taskEvents = array();
        foreach ($eventFilter as $key => $value) {
            if($value->repeat == 'yes'){
                   
                if ($value->repeat_type == 'day') {
                    $freq = 'DAILY';
                } else if ($value->repeat_type == 'week') {
                    $freq = 'WEEKLY';
                } else if ($value->repeat_type == 'month') {
                    $freq = 'MONTHLY';
                } else if ($value->repeat_type == 'year') {
                    $freq = 'YEARLY';
                }
                //     $arr =[];
                //    $rule = 'DTSTART:DATE\nRRULE:FREQ=MONTHLY;COUNT=6;INTERVAL=1';
                //    $search  = array("DATE","MONTHLY","6","1 ");
                //    $replace = array($value->start_date_time,$freq,$value->repeat_cycles,$value->repeat_every);
                //    $output = str_replace($search, $replace, $rule);
              
                    
                $taskEvents[] = [
                    'id' => $value->id,
                    'title' => $value->event_name,
                    'className' => $value->label_color,
                    'start' => $value->start_date_time,
                    'end' => $value->end_date_time,
                        
                ];
            }else{
                $taskEvents[] = [
                    'id' => $value->id,
                    'title' => $value->event_name,
                    'className' => $value->label_color,
                    'start' => $value->start_date_time,
                    'end' => $value->end_date_time
                ];
            }
                
        }
            return $taskEvents;
    }

    public function store(StoreEvent $request)
    {
        $eventIds = [];
        $event = new Event();
        $event->event_name = $request->event_name;
        $event->where = $request->where;
        $event->description = $request->description;
        $event->category_id = $request->category_id;
        $event->event_type_id = $request->event_type_id;
        $event->event_unique_id = $request->event_unique_id;


        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');
        if ($request->repeat) {
            $event->repeat = $request->repeat;
        } else {
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        $event->save();
        $eventIds [] = $event->id;
        if ($request->all_employees) {
            $attendees = User::allEmployees();
            foreach ($attendees as $attendee) {
                EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
            }

            Notification::send($attendees, new EventInvite($event));
        }
        if ($request->all_clients) {

            if(isset($attendees)){
                $attendees = User::allClients()->merge($attendees);
            }
            else{
                $attendees = User::allClients();
            }

            foreach ($attendees as $attendee) {
                EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
            }

            Notification::send($attendees, new EventInvite($event));
        }

        if ($request->user_id) {
            foreach ($request->user_id as $userId) {
                EventAttendee::firstOrCreate(['user_id' => $userId, 'event_id' => $event->id]);
            }
            $attendees = User::whereIn('id', $request->user_id)->get();
            Notification::send($attendees, new EventInvite($event));
        }
        if (!$request->has('repeat') || $request->repeat == 'no') {
            $event->event_id = $this->googleCalendarEvent($event);
            $event->save();
        }

        // Add repeated event
        if ($request->has('repeat') && $request->repeat == 'yes') {
            $repeatCount = $request->repeat_count;
            $repeatType = $request->repeat_type;
            $repeatCycles = $request->repeat_cycles;

            $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date);
            $dueDate = Carbon::createFromFormat($this->global->date_format, $request->end_date);

            for ($i = 1; $i < $repeatCycles; $i++) {
                $startDate = $startDate->add($repeatCount, str_plural($repeatType));
                $dueDate = $dueDate->add($repeatCount, str_plural($repeatType));

                $event = new Event();
                $event->event_name = $request->event_name;
                $event->where = $request->where;
                $event->description = $request->description;
                $event->start_date_time = $startDate->format('Y-m-d') . ' ' . Carbon::parse($request->start_time)->format('H:i:s');
                $event->end_date_time = $dueDate->format('Y-m-d') . ' ' . Carbon::parse($request->end_time)->format('H:i:s');
                $event->event_unique_id = $request->event_unique_id;

                if ($request->repeat) {
                    $event->repeat = $request->repeat;
                } else {
                    $event->repeat = 'no';
                }
                $event->repeat_every = $request->repeat_count;
                $event->repeat_cycles = $request->repeat_cycles;
                $event->repeat_type = $request->repeat_type;
                $event->label_color = $request->label_color;
                $event->save();

                if ($request->all_employees) {
                    $attendees = User::allEmployees();
                    foreach ($attendees as $attendee) {
                        EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
                    }
        
                    Notification::send($attendees, new EventInvite($event));
                }
        
                if ($request->user_id) {
                    foreach ($request->user_id as $userId) {
                        EventAttendee::firstOrCreate(['user_id' => $userId, 'event_id' => $event->id]);
                    }
                    $attendees = User::whereIn('id', $request->user_id)->get();
                    Notification::send($attendees, new EventInvite($event));
                }
                $eventIds [] = $event->id;
            }
            $this->googleCalendarEventMulti($eventIds);
        }
        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function edit($id)
    {
        $this->employees = User::allEmployees();
        $this->clients = User::allClients();
        $this->event = Event::with('attendee')->findOrFail($id);
        $this->categories = EventCategory::all();
        $this->eventTypes = EventType::all();
        $arr = [];
        foreach($this->event->attendee  as $emp){
            if(in_array($emp->user_id, $this->employees->pluck('id')->toArray())){
                $value = array_push($arr, $emp->user->name);
            }
        }
        $this->totalAttendee = $value;
        $view = view('admin.event-calendar.edit', $this->data)->render();
        return Reply::dataOnly(['status' => 'success', 'view' => $view]);
    }

    public function update(UpdateEvent $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->event_name = $request->event_name;
        $event->where = $request->where;
        $event->description = $request->description;
        $event->event_type_id = $request->event_type_id;
        $event->category_id = $request->category_id;
        $event->start_date_time = Carbon::createFromFormat($this->global->date_format, $request->start_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->start_time)->format('H:i:s');
        $event->end_date_time = Carbon::createFromFormat($this->global->date_format, $request->end_date)->format('Y-m-d') . ' ' . Carbon::createFromFormat($this->global->time_format, $request->end_time)->format('H:i:s');

        if ($request->repeat) {
            $event->repeat = $request->repeat;
        } else {
            $event->repeat = 'no';
        }

        $event->repeat_every = $request->repeat_count;
        $event->repeat_cycles = $request->repeat_cycles;
        $event->repeat_type = $request->repeat_type;
        $event->label_color = $request->label_color;
        $event->save();

        $event->event_id = $this->googleCalendarEvent($event);
        $event->save();


        if ($request->all_employees) {
            $attendees = User::allEmployees();
            foreach ($attendees as $attendee) {
                $checkExists = EventAttendee::where('user_id', $attendee->id)->where('event_id', $event->id)->first();
                if (!$checkExists) {
                    EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($attendee->id);
                    $notifyUser->notify(new EventInvite($event));
                }
            }
        }
        if ($request->all_clients) {
            $attendees = User::allClients();
            foreach ($attendees as $attendee) {
                $checkExists = EventAttendee::where('user_id', $attendee->id)->where('event_id', $event->id)->first();
                if (!$checkExists) {
                    EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($attendee->id);
                    $notifyUser->notify(new EventInvite($event));
                }
            }
        }

        if ($request->user_id) {
            $selectedClient = $request->selected_client;

            if($selectedClient)
            {
                $newSelectedClient = $request->user_id;
                $removedClient = array_diff($selectedClient, $newSelectedClient);
                if($removedClient)
                {
                    EventAttendee::whereIn('user_id', $removedClient)->where('event_id', $event->id)->delete();
                }
            }
           
            foreach ($request->user_id as $userId) {
                $checkExists = EventAttendee::where('user_id', $userId)->where('event_id', $event->id)->first();
                if (!$checkExists) {
                    EventAttendee::create(['user_id' => $userId, 'event_id' => $event->id]);

                    //      Send notification to user
                    $notifyUser = User::withoutGlobalScope('active')->findOrFail($userId);
                    $notifyUser->notify(new EventInvite($event));
                }
               
            }
        }
        $checkEvent = Event::where('event_unique_id', $request->event_unique_id )->get();
        $this->updateEvent($request, $event->start_date_time, $checkEvent );
        
        return Reply::success(__('messages.eventCreateSuccess'));
    }

    public function updateEvent($request,$start_date_time,$checkEvent)
    {
             Event::where('event_unique_id', $request->event_unique_id)->where('start_date_time', '!=', $start_date_time)->delete();
        if ($request->has('repeat') && $request->repeat == 'yes') {
            $repeatCount = $request->repeat_count;
            $repeatType = $request->repeat_type;
            $repeatCycles = $request->repeat_cycles;
    
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->start_date);
            $dueDate = Carbon::createFromFormat($this->global->date_format, $request->end_date);
    
            for ($i = 1; $i < $repeatCycles; $i++) {
                $startDate = $startDate->add($repeatCount, str_plural($repeatType));
                $dueDate = $dueDate->add($repeatCount, str_plural($repeatType));
    
                $event = new Event();
                $event->event_name = $request->event_name;
                $event->where = $request->where;
                $event->description = $request->description;
                $event->start_date_time = $startDate->format('Y-m-d') . ' ' . Carbon::parse($request->start_time)->format('H:i:s');
                $event->end_date_time = $dueDate->format('Y-m-d') . ' ' . Carbon::parse($request->end_time)->format('H:i:s');
                $event->event_unique_id = $request->event_unique_id;
    
                if ($request->repeat) {
                    $event->repeat = $request->repeat;
                } else {
                    $event->repeat = 'no';
                }

                $event->repeat_every = $request->repeat_count;
                $event->repeat_cycles = $request->repeat_cycles;
                $event->repeat_type = $request->repeat_type;
                $event->label_color = $request->label_color;
                $event->save();
                if ($request->all_employees) {
                    $attendees = User::allEmployees();
                    foreach ($attendees as $attendee) {
                        EventAttendee::create(['user_id' => $attendee->id, 'event_id' => $event->id]);
                    }
            
                    Notification::send($attendees, new EventInvite($event));
                }
            
                if ($request->user_id) {
                    foreach ($request->user_id as $userId) {
                        EventAttendee::firstOrCreate(['user_id' => $userId, 'event_id' => $event->id]);
                    }
                    $attendees = User::whereIn('id', $request->user_id)->get();
                    Notification::send($attendees, new EventInvite($event));
                }
            }
        }
    }

    public function show($id)
    {
        $this->startDate = explode(' ', request('start'));
        $this->startDate = Carbon::parse($this->startDate[0]);
        $this->event = Event::with(['attendee', 'attendee.user'])->findOrFail($id);
        return view('admin.event-calendar.show', $this->data);
    }

    public function removeAttendee(Request $request)
    {
        EventAttendee::destroy($request->attendeeId);
        $id = $request->event_id;
        $employees = User::doesntHave('attendee', 'and', function ($query) use ($id) {
            $query->where('event_id', $id);
        })
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->get();

        $employeesArray = [];

        foreach ($employees as $key => $employee) {
            $employeesArray[$key]['id'] = $employee->id;
            $employeesArray[$key]['text'] = (auth()->user()->id == $employee->id) ? $employeesArray[$key]['text'] = $employee->name . ' (You)' : $employeesArray[$key]['text'] = $employee->name;
        }

        return Reply::dataOnly(['status' => 'success', 'employees' => $employeesArray]);
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return Reply::success(__('messages.eventDeleteSuccess'));
    }

    protected function googleCalendarEventMulti($eventIds)
    {
        if (company() && global_settings()->google_calendar_status == 'active') {

            $google = new Google();
            $company = company();
            $events = Event::whereIn('id', $eventIds)->get();
            $event = $events->first();

            $frq = ['day' => 'DAILY', 'week' => 'WEEKLY', 'month', 'MONTHLY','year' => 'YEARLY'];
            $frequency = $frq[$event->repeat_type];
            $googleAccount = GoogleAccount::where('company_id', company()->id)->first();;

            $eventData = new \Google_Service_Calendar_Event();
            $eventData->setSummary($event->event_name);
            $eventData->setLocation($event->where);
            $start = new \Google_Service_Calendar_EventDateTime();
            $start->setDateTime($event->start_date_time->toAtomString());
            $start->setTimeZone(company()->timezone);
            $eventData->setStart($start);
            $end = new \Google_Service_Calendar_EventDateTime();
            $end->setDateTime($event->end_date_time->toAtomString());
            $end->setTimeZone(company()->timezone);
            $eventData->setEnd($end);

            $eventData->setRecurrence(array('RRULE:FREQ='.$frequency.';INTERVAL='.$event->repeat_every.';COUNT='.$event->repeat_cycles.';'));

            $attendees = EventAttendee::with(['user'])->where('event_id', $event->id)->get();
            $attendiesData = [];
            foreach($attendees as $attend){
                if(!is_null($attend->user) && !is_null($attend->user->email) && !is_null($attend->user->calendar_module) && $attend->user->calendar_module->event_status)
                {
                    $attendee1 = new \Google_Service_Calendar_EventAttendee();
                    $attendee1->setEmail($attend->user->email);
                    $attendiesData[] = $attendee1;
                }
            }

            $eventData->attendees = $attendiesData;

            if ((global_settings()->google_calendar_status == 'active') && $googleAccount) {
                // Create event
                $google->connectUsing($googleAccount->token);
                // array for multiple

                try {
                    if ($event->event_id) {
                        $results = $google->service('Calendar')->events->patch('primary', $event->event_id, $eventData);
                    } else {
                        $results = $google->service('Calendar')->events->insert('primary', $eventData);
                    }
                    foreach($events as $event){
                        $event->event_id = $results->id;
                        $event->save();
                    }
                    return;
                } catch (\Google\Service\Exception $th) {
                    $googleAccount->delete();
                    $google->revokeToken($googleAccount->token);
                }
            }
            foreach($events as $event){
                $event->event_id = $event->event_id;
                $event->save();
            }
            return;
        }
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
