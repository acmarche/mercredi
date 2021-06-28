<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class AnneeScolaireCreated
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
