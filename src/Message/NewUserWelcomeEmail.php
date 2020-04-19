<?php


namespace AcMarche\Mercredi\Message;


class NewUserWelcomeEmail
{
    private $enfantId;

    public function __construct(int $enfantId)
    {
        $this->enfantId = $enfantId;
    }

    public function getEnfantId(): int
    {
        return $this->enfantId;
    }
}
