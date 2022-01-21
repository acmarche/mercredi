<?php

namespace AcMarche\Mercredi\Registration\Message;

final class RegisterCreated
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
