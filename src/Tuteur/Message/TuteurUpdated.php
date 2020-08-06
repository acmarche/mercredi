<?php

namespace AcMarche\Mercredi\Tuteur\Message;

final class TuteurUpdated
{
    /**
     * @var int
     */
    private $tuteurId;

    public function __construct(int $tuteurId)
    {
        $this->tuteurId = $tuteurId;
    }

    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }
}
