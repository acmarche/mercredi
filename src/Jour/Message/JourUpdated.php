<?php

namespace AcMarche\Mercredi\Jour\Message;

final class JourUpdated
{
    public function __construct(
        private int $jourId
    ) {
    }

    public function getJourId(): int
    {
        return $this->jourId;
    }
}
