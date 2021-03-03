<?php

namespace App\Listeners;

use App\Events\LeadStatusUpdatedEvent;
use App\Interfaces\MailingSystemInterface;

class SyncUserWithMailingSystem
{
    public function __construct(public MailingSystemInterface $mailingSystem)
    {
        //
    }

    // receives the user's email - name - lastname -- and list id
    public function handle(LeadStatusUpdatedEvent $event) : void
    {
        $this->mailingSystem->subscribeUser($event->email, $event->firstname, $event->lastname, $event->listId);
    }
}
