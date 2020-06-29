<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

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
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(
        PlaineRepository $plaineRepository,
        JourRepository $jourRepository,
        PresenceRepository $presenceRepository
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->jourRepository = $jourRepository;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     *
     */
    public function handleEditJours()
    {
        $this->jourRepository->flush();
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
