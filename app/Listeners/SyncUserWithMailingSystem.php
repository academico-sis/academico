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
    public function handle(LeadStatusUpdatedEvent $event): void
    {
        if (config('mailing-system.external_mailing_enabled') && $event->email && $event->firstname && $event->lastname && $event->listId) {
            $this->mailingSystem->subscribeUser($event->email, $event->firstname, $event->lastname, $event->listId);
        }
    }
}
