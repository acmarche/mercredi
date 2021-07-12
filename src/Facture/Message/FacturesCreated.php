<?php

namespace AcMarche\Mercredi\Facture\Message;

final class FacturesCreated
{
    private array $factureIds;

    public function __construct(array $factureIds)
    {
        $this->factureIds = $factureIds;
    }

    public function getFactureIds(): array
    {
        return $this->factureIds;
    }
}
