<?php

namespace App\Events;

use App\Contract;
use App\ContractSign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractSignedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contract;
    public $contractSign;
    public $notifyUser;

    public function __construct(Contract $contract, $notifyUser, ContractSign $contractSign)
    {
        $this->contract = $contract;
        $this->contractSign = $contractSign;
        $this->notifyUser = $notifyUser;
    }

}
