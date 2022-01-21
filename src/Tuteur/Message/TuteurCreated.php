<?php

namespace AcMarche\Mercredi\Tuteur\Message;

final class TuteurCreated
{
    public function __construct(
        private int $tuteurId
    ) {
    }

    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }
}
