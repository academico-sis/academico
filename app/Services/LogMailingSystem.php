<?php


namespace App\Services;


class LogMailingSystem implements \App\Interfaces\MailingSystemInterface
{

    public function subscribeUser(string $email, string $name, ?string $lastname, int $listId): void
    {
        // TODO: Implement subscribeUser() method.
    }

    public function unsubscribeUser(string $email, int $listId): void
    {
        // TODO: Implement unsubscribeUser() method.
    }
}
