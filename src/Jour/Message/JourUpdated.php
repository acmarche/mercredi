<?php


namespace AcMarche\Mercredi\Jour\Message;

class JourUpdated
{
    /**
     * @var int
     */
    private $jourId;

    public function __construct(int $jourId)
    {
        $this->jourId = $jourId;
    }

    /**
     * @return int
     */
    public function getJourId(): int
    {
        return $this->jourId;
    }
}
