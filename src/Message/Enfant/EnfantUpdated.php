<?php


namespace AcMarche\Mercredi\Message\Enfant;


use AcMarche\Mercredi\Entity\Enfant;

class EnfantUpdated
{
    /**
     * @var int
     */
    private $enfantId;

    public function __construct(int $enfantId)
    {
        $this->enfantId = $enfantId;
    }

    /**
     * @return int
     */
    public function getEnfantId(): int
    {
        return $this->enfantId;
    }

}
