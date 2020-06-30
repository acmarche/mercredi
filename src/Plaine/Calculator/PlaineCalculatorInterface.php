<?php


namespace AcMarche\Mercredi\Plaine\Calculator;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;

interface PlaineCalculatorInterface
{
    public function calculate(Plaine $plaine, Enfant $enfant): float;
}
