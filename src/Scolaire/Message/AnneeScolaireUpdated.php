<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class AnneeScolaireUpdated
{
    public function __construct(
        private int $anneeScolaireId
    ) {
    }

    public function getAnneeScolaireId(): int
    {
        return $this->anneeScolaireId;
    }
}
