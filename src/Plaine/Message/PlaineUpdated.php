<?php

namespace AcMarche\Mercredi\Plaine\Message;

class PlaineUpdated
{
    /**
     * @var int
     */
    private $plaineId;

    public function __construct(int $plaineId)
    {
        $this->plaineId = $plaineId;
    }

    public function getPlaineId(): int
    {
        return $this->plaineId;
    }
}
