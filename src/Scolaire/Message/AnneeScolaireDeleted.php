<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class AnneeScolaireDeleted
{
    private int $anneeScolaireId;

    public function __construct(int $anneeScolaireId)
    {
        $this->anneeScolaireId = $anneeScolaireId;
    }

    public function getAnneeScolaireId(): int
    {
        return $this->anneeScolaireId;
    }
}
