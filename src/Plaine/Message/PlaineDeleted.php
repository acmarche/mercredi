<?php

namespace AcMarche\Mercredi\Plaine\Message;

final class PlaineDeleted
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
