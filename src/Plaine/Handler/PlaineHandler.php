<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineJour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
    /**
     * @var PlaineJourRepository
     */
    private $plaineJourRepository;

    public function __construct(
        PlaineRepository $plaineRepository,
        JourRepository $jourRepository,
        PresenceRepository $presenceRepository,
        PlaineJourRepository $plaineJourRepository
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->jourRepository = $jourRepository;
        $this->presenceRepository = $presenceRepository;
        $this->plaineJourRepository = $plaineJourRepository;
    }

    /**
     * @param array $currentsJours
     * @param iterable|ArrayCollection $newJours
     */
    public function handleEditJours(Plaine $plaine, array $currentJours, iterable $newJours)
    {
        $enMoins = array_diff($currentJours, $newJours->toArray());

        foreach ($newJours as $jour) {
            $date = $jour->getDateJour();
            if ($jourExistant = $this->jourRepository->findOneByDateJour($date)) {
                $plaineJour = new PlaineJour($plaine, $jourExistant);
            } else {
                $this->jourRepository->persist($jour);
                $plaineJour = new PlaineJour($plaine, $jour);
            }
            $this->plaineJourRepository->persist($plaineJour);
            $plaine->addPlaineJour($plaineJour);
        }

        foreach ($enMoins as $jour) {
            //     $plaine->removePlaineJour($jour);
        }

        $this->jourRepository->flush();
        $this->plaineJourRepository->flush();
        $this->plaineRepository->flush();
    }

    /**
     * Ajoute au moins deux dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $plaine->initJours();
        $today = new Jour(new \DateTime('first day of january this year'));
        $tomorrow = new Jour(new \DateTime('+1day'));
        $plaine->addJour($today);
        $plaine->addJour($tomorrow);
    }

    /**
     * @return Jour[]
     */
    public function findJoursByPlaine(Plaine $plaine): array
    {
        return $this->plaineJourRepository->findBy(['plaine' => $plaine]);
    }
}
