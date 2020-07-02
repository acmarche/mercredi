<?php

namespace AcMarche\Mercredi\Scolaire\Message;

class AnneeScolaireUpdated
{
    /**
     * @var int
     */
    private $anneeScolaireId;

    public function __construct(int $anneeScolaireId)
    {
        $this->anneeScolaireId = $anneeScolaireId;
    }

    public function getAnneeScolaireId(): int
    {
        return $this->anneeScolaireId;
    }
}
