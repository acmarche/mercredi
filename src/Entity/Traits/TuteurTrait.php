<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Tuteur;

trait TuteurTrait
{
    /**
     * @var Tuteur
     */
    private $tuteur;

    public function getTuteur(): Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
    }
}
