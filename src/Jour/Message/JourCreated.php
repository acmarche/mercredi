<?php

namespace AcMarche\Mercredi\Jour\Message;

class JourCreated
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
