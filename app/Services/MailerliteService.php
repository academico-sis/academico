<?php

namespace App\Services;

use App\Interfaces\MailingSystemInterface;
use MailerLiteApi\Api\Groups;
use MailerLiteApi\Api\Subscribers;
use MailerLiteApi\MailerLite;

class MailerliteService implements MailingSystemInterface
{
    public Groups $groupsApi;

    public Subscribers $subscribersApi;

    public function __construct()
    {
        $api_key = config('mailing-system.mailerlite.key');

        $this->groupsApi = (new MailerLite($api_key))->groups();
        $this->subscribersApi = (new MailerLite($api_key))->subscribers();
    }

    public function subscribeUser($email, $name, $lastname, $listId): void
    {
        $subscriberGroups = $this->subscribersApi->getGroups($email); // returns array of group objects subscriber belongs to

        foreach ($subscriberGroups as $group) {
            $groupId = $array = json_decode(json_encode($group, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            $this->groupsApi->removeSubscriber($groupId['id'], $email); // returns empty response
        }

        $subscriber = [
            'email' => $email,
            'name' => $name,
            'fields' => [
                'lastname' => $lastname,
            ],
        ];

        $this->groupsApi->addSubscriber($listId, $subscriber); // returns added subscriber
    }

    public function unsubscribeUser(string $email, $listId): void
    {
        // TODO: Implement unsubscribeUser() method.
    }
}
