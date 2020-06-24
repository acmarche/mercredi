<?php

namespace AcMarche\Mercredi\Facture\Message;

class FactureUpdated
{
    /**
     * @var int
     */
    private $factureId;

    public function __construct(int $factureId)
    {
        $this->factureId = $factureId;
    }

    public function getFactureId(): int
    {
        return $this->factureId;
    }
}
