<?php

namespace AcMarche\Mercredi\User\Message;

final class UserDeleted
{
    public function __construct(
        private int $userId
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
