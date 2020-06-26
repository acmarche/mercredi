<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;

class PlaineHandler
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(PlaineRepository $plaineRepository, JourRepository $jourRepository)
    {
        $this->plaineRepository = $plaineRepository;
        $this->jourRepository = $jourRepository;
    }

    /**
     * @param Jour[] $jours
     */
    public function handleJours(Plaine $plaine, iterable $jours)
    {
        $this->jourRepository->flush();
        $this->plaineRepository->flush();

        return;
        foreach ($jours as $jour) {
            if (null === $jour) {
                continue;
            }
            $jour = $this->jourRepository->findOneBy(['date_jour' => $date]);
            if ($jour) {
            } else {
                $jour = new Jour();
                $jour->setDateJour($date);

                $this->jourRepository->persist($jour);
                $this->jourRepository->flush();
            }
            $plaine->addJour($jour);
        }
        $this->plaineRepository->flush();
    }

    /**
     * Ajoute au moins deux dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $jours = $plaine->getJours();
        if (0 == $jours->count()) {
            $today = new Jour(new \DateTime());
            $tomorrow = new Jour(new \DateTime('+1day'));
            $plaine->addJour($today);
            $plaine->addJour($tomorrow);
        }
    }
}
