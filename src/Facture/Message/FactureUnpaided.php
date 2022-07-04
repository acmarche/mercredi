<?php

namespace AcMarche\Mercredi\Facture\Message;

final class FactureUnpaided
{
    public function __construct(
        private int $factureId
    ) {
    }

    public function getFactureId(): int
    {
        return $this->factureId;
    }
}
