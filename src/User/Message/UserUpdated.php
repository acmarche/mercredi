<?php

namespace AcMarche\Mercredi\User\Message;

class UserUpdated
{
    /**
     * @var int
     */
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
