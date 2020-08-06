<?php

namespace AcMarche\Mercredi\Jour\Message;

final class JourDeleted
{
    /**
     * @var int
     */
    private $jourId;

    public function __construct(int $jourId)
    {
        $this->jourId = $jourId;
    }

    public function getJourId(): int
    {
        return $this->jourId;
    }
}
