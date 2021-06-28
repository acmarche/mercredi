<?php

namespace AcMarche\Mercredi\Facture\Message;

final class FactureUpdated
{
    private int $factureId;

    public function __construct(int $factureId)
    {
        $this->factureId = $factureId;
    }

    public function getFactureId(): int
    {
        return $this->factureId;
    }
}
