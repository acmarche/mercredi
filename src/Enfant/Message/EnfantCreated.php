<?php

namespace AcMarche\Mercredi\Enfant\Message;

final class EnfantCreated
{
    public function __construct(
        private int $enfantId
    ) {
    }

    public function getEnfantId(): int
    {
        return $this->enfantId;
    }
}
