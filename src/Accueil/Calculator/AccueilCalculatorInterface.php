<?php


namespace AcMarche\Mercredi\Accueil\Calculator;


use AcMarche\Mercredi\Entity\Accueil;

interface AccueilCalculatorInterface
{
    public function calculate(Accueil $accueil): float;

}
