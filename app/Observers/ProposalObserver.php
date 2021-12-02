<?php

namespace App\Observers;

use App\Events\NewProposalEvent;
use App\Notifications\NewClientProposal;
use App\Proposal;
use App\Notification;

class ProposalObserver
{

    public function creating(Proposal $proposal)
    {
        if (request()->status_type && (request()->status_type == 'save' || request()->status_type == 'draft')) {
            $proposal->send_status = 0;
        }

        if (request()->status_type == 'draft') {
            $proposal->status = 'draft';
        }
    }

    public function saving(Proposal $proposal)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $proposal->company_id = company()->id;
        }
    }

    /**
     * @param Proposal $proposal
     */
    public function created(Proposal $proposal)
    {
        if ($proposal->lead) {
            if (request()->status_type != 'save' && request()->status_type != 'draft') {
                $proposal->lead->notify(new NewClientProposal($proposal));
                $type = 'new';
                event(new NewProposalEvent($proposal, $type));
            }
        }
   
    }

    /** @param Proposal $proposal
     */
    // public function updated(Proposal $proposal)
    // {
    //     if ($proposal->status) {
    //         $type = 'statusUpdate';
    //         event(new NewProposalEvent($proposal, $type));
    //     }
    // }

    public function deleting(Proposal $proposal)
    {
        $notifiData = ['App\Notifications\NewProposal','App\Notifications\ProposalSigned'
        ];
        $notifications = Notification::
          whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$proposal->id.',%')
            ->delete();
    }

}
