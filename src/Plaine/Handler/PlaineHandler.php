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
     *
     * @param Jour[]|ArrayCollection $newJours
     */
    public function handleEditJours(Plaine $plaine, iterable $newJours)
    {
        $enMoins = $this->getEnMoins($plaine, $newJours);

        foreach ($newJours as $plaineJour) {
            if ($jourExistant = $this->jourExist($plaineJour)) {
                if (!$this->plaineJourExist($plaine, $jourExistant)) {
                    $plaineJour = new PlaineJour($plaine, $jourExistant);
                    $plaine->addPlaineJour($plaineJour);
                }
            } else {
                $this->jourRepository->persist($plaineJour);
                $plaineJour = new PlaineJour($plaine, $plaineJour);
                $this->plaineJourRepository->persist($plaineJour);
                $plaine->addPlaineJour($plaineJour);
            }
        }

        foreach ($enMoins as $plaineJour) {
            $this->plaineJourRepository->remove($plaineJour);
        }

        $this->jourRepository->flush();
        $this->plaineJourRepository->flush();
        $this->plaineRepository->flush();
    }

    private function jourExist(Jour $jour): ?Jour
    {
        $date = $jour->getDateJour();

        return $this->jourRepository->findOneByDateJour($date);
    }

    private function plaineJourExist(Plaine $plaine, Jour $jour): ?PlaineJour
    {
        return $this->plaineJourRepository->findByPlaineAndJour($plaine, $jour);
    }

    /**
     * Ajoute au moins deux dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $plaine->initJours();
        $currentJours = $this->findPlaineJoursByPlaine($plaine);
        if (count($currentJours) == 0) {
            $today = new Jour(new \DateTime('first day of january this year'));
            $tomorrow = new Jour(new \DateTime('+1day'));
            $plaine->addJour($today);
            $plaine->addJour($tomorrow);
        } else {
            foreach ($currentJours as $jour) {
                $plaine->addJour($jour->getJour());
            }
        }
    }

    /**
     * @return PlaineJour[]
     */
    public function findPlaineJoursByPlaine(Plaine $plaine): array
    {
        return $this->plaineJourRepository->findByPlaine($plaine);
    }

    /**
     * @param Plaine $plaine
     * @param Jour[] $newJours
     * @return PlaineJour[]
     */
    private function getEnMoins(Plaine $plaine, iterable $newJours): array
    {
        $currentJours = $this->findPlaineJoursByPlaine($plaine);
        $enMoins = [];
        foreach ($currentJours as $plaineJour) {
            $found = false;
            $jourExistant = $plaineJour->getJour();
            foreach ($newJours as $newJour) {
                if ($jourExistant->getDateJour()->format('Y-m-d') == $newJour->getDateJour()->format('Y-m-d')) {
                    $found= true;
                    break;
                }
            }
            if($found===false) {
                $enMoins[] = $plaineJour;
            }
        }
        return $enMoins;
    }
}
