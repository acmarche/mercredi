<?php

namespace AcMarche\Mercredi\Accueil\Message;

final class AccueilCreated
{
    public function __construct(
        private int $accueilId
    ) {
    }

    public function getAccueilId(): int
    {
        return $this->accueilId;
    }
}
