<?php

namespace AcMarche\Mercredi\Accueil\Calculator;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Parameter\Option;
use Carbon\Carbon;
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

    /**
     * 18h15 => 0.25, 18h30 => 0.25
     * 18h31 => 0.5 et  18h46 => 0.5.
     */
    public function calculateRetard(Accueil $accueil): float
    {
        $heureRetard = $accueil->getHeureRetard();
        if (null !== $heureRetard) {
            $h18 = Carbon::instance($heureRetard);
            $h18->hour(18);
            $h18->minute(15);
            $minutes = $h18->diffInMinutes($heureRetard);
            if ($minutes > 45) {
                return 1.5;
            }
            if ($minutes > 30) {
                return 1.0;
            }
            if ($minutes > 15) {
                return 0.5;
            }

            return 0.25;
        }

        return 0;
    }
}
