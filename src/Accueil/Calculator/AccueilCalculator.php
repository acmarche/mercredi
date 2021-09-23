<?php

namespace AcMarche\Mercredi\Accueil\Calculator;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Parameter\Option;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class AccueilCalculator implements AccueilCalculatorInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function calculate(Accueil $accueil): float
    {
        $prix = $this->getPrix($accueil);

        return $accueil->getDuree() * $prix;
    }

    public function getPrix(Accueil $accueil): float
    {
        return $this->parameterBag->get(Option::ACCUEIL_PRIX) ?? 0;
    }
}
