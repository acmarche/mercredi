<?php

namespace AcMarche\Mercredi\Relation\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;

final class TuteurEnfantDto
{
    use EnfantTrait;
    use TuteurTrait;

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }
}
