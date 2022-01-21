<?php

namespace AcMarche\Mercredi\Plaine\Message;

final class PlaineUpdated
{
    public function __construct(
        private int $plaineId
    ) {
    }

    public function getPlaineId(): int
    {
        return $this->plaineId;
    }
}
