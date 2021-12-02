<?php

namespace App\Providers;

use App\Events\AttendanceReminderEvent;
use App\Events\AutoTaskReminderEvent;
use App\Events\CompanyRegistered;
use App\Events\ContractSignedEvent;
use App\Events\DiscussionEvent;
use App\Events\DiscussionReplyEvent;
use App\Events\InvoicePaymentEvent;
use App\Events\InvoiceReminderEvent;
use App\Events\LeadEvent;
use App\Events\LeaveEvent;
use App\Events\NewCreditNoteEvent;
use App\Events\NewExpenseEvent;
use App\Events\NewExpenseRecurringEvent;
use App\Events\NewInvoiceRecurringEvent;
use App\Events\NewNoticeEvent;
use App\Events\NewProjectEvent;
use App\Events\NewProjectMemberEvent;
use App\Events\NewProposalEvent;
use App\Events\NewSupportTicketEvent;
use App\Events\PaymentReminderEvent;
use App\Events\ProjectFileEvent;
use App\Events\ProjectReminderEvent;
use App\Events\RatingEvent;
use App\Events\SubTaskCompletedEvent;
use App\Events\SupportTicketAgentEvent;
use App\Events\SupportTicketReplyEvent;
use App\Events\SupportTicketRequesterEvent;
use App\Events\TaskCommentEvent;
use App\Events\TaskEvent;
use App\Events\TaskNoteEvent;
use App\Events\TaskReminderEvent;
use App\Events\TicketReplyEvent;
use App\Events\TicketRequesterEvent;
use App\Listeners\AttendanceReminderListener;
use App\Listeners\AutoTaskReminderListener;
use App\Listeners\CompanyRegisteredListener;
use App\Listeners\ContractSignedListener;
use App\Listeners\DiscussionListener;
use App\Listeners\DiscussionReplyListener;
use App\Listeners\InvoicePaymentListener;
use App\Listeners\InvoiceReminderListener;
use App\Listeners\LeadListener;
use App\Listeners\LeaveListener;
use App\Listeners\NewCreditNoteListener;
use App\Listeners\NewExpenseListener;
use App\Listeners\NewExpenseRecurringListener;
use App\Listeners\NewInvoiceRecurringListener;
use App\Listeners\NewNoticeListener;
use App\Listeners\NewProjectListener;
use App\Listeners\NewProjectMemberListener;
use App\Listeners\NewProposalListener;
use App\Listeners\NewSupportTicketListener;
use App\Listeners\PaymentReminderListener;
use App\Listeners\ProjectFileListener;
use App\Listeners\ProjectReminderListener;
use App\Listeners\RatingListener;
use App\Listeners\SubTaskCompletedListener;
use App\Listeners\SupportTicketAgentListener;
use App\Listeners\SupportTicketReplyListener;
use App\Listeners\SupportTicketRequesterListener;
use App\Listeners\TaskCommentListener;
use App\Listeners\TaskListener;
use App\Listeners\TaskNoteListener;
use App\Listeners\TaskReminderListener;
use App\Listeners\TicketReplyListener;
use App\Listeners\TicketRequesterListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CompanyRegistered::class => [CompanyRegisteredListener::class],
        TaskEvent::class => [TaskListener::class],
        TaskReminderEvent::class => [TaskReminderListener::class],
        TaskCommentEvent::class => [TaskCommentListener::class],
        AutoTaskReminderEvent::class => [AutoTaskReminderListener::class],
        SubTaskCompletedEvent::class => [SubTaskCompletedListener::class],
        DiscussionReplyEvent::class => [DiscussionReplyListener::class],
        DiscussionEvent::class => [DiscussionListener::class],
        TicketReplyEvent::class => [TicketReplyListener::class],
        TaskNoteEvent::class => [TaskNoteListener::class],
        TicketRequesterEvent::class => [TicketRequesterListener::class],
        NewExpenseRecurringEvent::class => [NewExpenseRecurringListener::class],
        NewInvoiceRecurringEvent::class => [NewInvoiceRecurringListener::class],
        NewCreditNoteEvent::class => [NewCreditNoteListener::class],
        LeadEvent::class => [LeadListener::class],
        NewSupportTicketEvent::class => [NewSupportTicketListener::class],
        SupportTicketAgentEvent::class => [SupportTicketAgentListener::class],
        SupportTicketReplyEvent::class => [SupportTicketReplyListener::class],
        SupportTicketRequesterEvent::class => [SupportTicketRequesterListener::class],
        NewProjectEvent::class => [NewProjectListener::class],
        ProjectFileEvent::class => [ProjectFileListener::class],
        RatingEvent::class => [RatingListener::class],
        LeaveEvent::class => [LeaveListener::class],
        NewExpenseEvent::class => [NewExpenseListener::class],
        NewNoticeEvent::class => [NewNoticeListener::class],
        NewProjectMemberEvent::class => [NewProjectMemberListener::class],
        PaymentReminderEvent::class => [PaymentReminderListener::class],
        NewProposalEvent::class => [NewProposalListener::class],
        InvoiceReminderEvent::class => [InvoiceReminderListener::class],
        AttendanceReminderEvent::class => [AttendanceReminderListener::class],
        InvoicePaymentEvent::class => [InvoicePaymentListener::class],
        ContractSignedEvent::class => [ContractSignedListener::class],
        ProjectReminderEvent::class => [ProjectReminderListener::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

}
