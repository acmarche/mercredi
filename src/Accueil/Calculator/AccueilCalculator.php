<?php

namespace AcMarche\Mercredi\Accueil\Calculator;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use Carbon\Carbon;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class AccueilCalculator implements AccueilCalculatorInterface
{
    public function __construct(
        #[Autowire(env: 'MERCREDI_ACCUEIL_PRIX')]
        private float $accueilPrix
    ) {
    }

    public function calculate(Accueil $accueil): float
    {
        $prix = $this->getPrix($accueil);

        return $accueil->getDuree() * $prix;
    }

    public function getPrix(Accueil $accueil): float
    {
        return $this->accueilPrix ?? 0;
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
            $minutes = $h18->diffInMinutes($heureRetard, true);
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
