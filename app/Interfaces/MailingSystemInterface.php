<?php

namespace App\Interfaces;

use App\Models\User;

interface MailingSystemInterface
{
    public function subscribeUser(string $email, string $name, ?string $lastname, int $listId) : void;

    public function unsubscribeUser(string $email, int $listId) : void;
}
