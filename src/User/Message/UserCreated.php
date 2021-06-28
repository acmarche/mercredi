<?php

namespace AcMarche\Mercredi\User\Message;

final class UserCreated
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
