<?php

namespace AcMarche\Mercredi\Enfant\Message;

final class EnfantDeleted
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
