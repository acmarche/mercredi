<?php

namespace AcMarche\Mercredi\Plaine\Message;

final class PlaineCreated
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
