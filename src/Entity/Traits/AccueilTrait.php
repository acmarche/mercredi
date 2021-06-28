<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Accueil;

trait AccueilTrait
{
    private ?Accueil $accueil;

    public function getAccueil(): ?Accueil
    {
        return $this->accueil;
    }

    public function setAccueil(?Accueil $accueil): void
    {
        $this->accueil = $accueil;
    }
}
