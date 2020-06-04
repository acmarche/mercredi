<?php

namespace AcMarche\Mercredi\Enfant\Message;

class EnfantCreated
{
    /**
     * @var int
     */
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
