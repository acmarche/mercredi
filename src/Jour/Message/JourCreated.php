<?php

namespace AcMarche\Mercredi\Jour\Message;

final class JourCreated
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
