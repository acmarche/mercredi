<?php

namespace AcMarche\Mercredi\Jour\Message;

final class JourCreated
{
    private int $jourId;

    public function __construct(int $jourId)
    {
        $this->jourId = $jourId;
    }

    public function getJourId(): int
    {
        return $this->jourId;
    }
}
