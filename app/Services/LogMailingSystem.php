<?php

namespace App\Services;

use App\Interfaces\MailingSystemInterface;
use Illuminate\Support\Facades\Log;

class LogMailingSystem implements MailingSystemInterface
{
    public function subscribeUser(string $email, string $name, ?string $lastname, int $listId): void
    {
        Log::info('MailingSystem: subscribeUser', [
            'email' => $email,
            'name' => $name,
            'lastname' => $lastname,
            'list_id' => $listId,
        ]);
    }

    public function unsubscribeUser(string $email, int $listId): void
    {
        Log::info('MailingSystem: unsubscribeUser', [
            'email' => $email,
            'list_id' => $listId,
        ]);
    }
}
