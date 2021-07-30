<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Presence\Accueil;

trait AccueilTrait
{
    private ?Accueil $accueil = null;

    public function getAccueil(): ?Accueil
    {
        return $this->accueil;
    }

    public function setAccueil(?Accueil $accueil): void
    {
        $this->accueil = $accueil;
    }
}
