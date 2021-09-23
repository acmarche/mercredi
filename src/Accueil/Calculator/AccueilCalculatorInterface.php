<?php

namespace AcMarche\Mercredi\Accueil\Calculator;

use AcMarche\Mercredi\Entity\Presence\Accueil;

interface AccueilCalculatorInterface
{
    public function getPrix(Accueil $accueil): float;

    public function calculate(Accueil $accueil): float;
}
