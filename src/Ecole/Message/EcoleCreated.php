<?php

namespace AcMarche\Mercredi\Ecole\Message;

final class EcoleCreated
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
