<?php

namespace AcMarche\Mercredi\Facture\Message;

final class FacturesCreated
{
    public function __construct(
        private array $factureIds
    ) {
    }

    public function getFactureIds(): array
    {
        return $this->factureIds;
    }
}
