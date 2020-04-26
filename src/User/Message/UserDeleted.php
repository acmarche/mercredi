<?php

namespace AcMarche\Mercredi\User\Message;

class UserDeleted
{
    /**
     * @var int
     */
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}
