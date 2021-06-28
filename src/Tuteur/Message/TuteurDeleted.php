<?php

namespace AcMarche\Mercredi\Tuteur\Message;

final class TuteurDeleted
{
    private int $tuteurId;

    public function __construct(int $tuteurId)
    {
        $this->tuteurId = $tuteurId;
    }

    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }
}
