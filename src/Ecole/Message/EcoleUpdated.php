<?php

namespace AcMarche\Mercredi\Ecole\Message;

final class EcoleUpdated
{
    public function __construct(
        private int $ecoleId
    ) {
    }

    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}
