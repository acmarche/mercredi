<?php

namespace AcMarche\Mercredi\Enfant\Message;

final class EnfantCreated
{
    private int $enfantId;

    public function __construct(int $enfantId)
    {
        $this->enfantId = $enfantId;
    }

    public function getEnfantId(): int
    {
        return $this->enfantId;
    }
}
