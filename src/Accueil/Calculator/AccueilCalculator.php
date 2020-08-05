<?php

namespace AcMarche\Mercredi\Accueil\Calculator;

use AcMarche\Mercredi\Entity\Accueil;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AccueilCalculator implements AccueilCalculatorInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function calculate(Accueil $accueil): float
    {
        $prix = $this->parameterBag->get('mercredi.accueil_prix') ?? 0;

        return $accueil->getDuree() * $prix;
    }
}
