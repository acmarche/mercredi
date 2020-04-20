<?php


namespace AcMarche\Mercredi\Message\Tuteur;

class TuteurUpdated
{
    /**
     * @var int
     */
    private $tuteurId;

    public function __construct(int $tuteurId)
    {
        $this->tuteurId = $tuteurId;
    }

    /**
     * @return int
     */
    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }

}
