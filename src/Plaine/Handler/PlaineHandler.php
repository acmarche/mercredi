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
     * @param Jour[]|ArrayCollection $newJours
     */
    public function handleEditJours(Plaine $plaine, iterable $newJours)
    {
        foreach ($newJours as $jour) {
            $jourEntity = $this->getJourEntityByJour($jour);
            $plaineJour = $this->getPlaineJourByPlaineAndJour($plaine, $jourEntity);
            $plaine->addPlaineJour($plaineJour);
        }

        $this->removePlaineJours($plaine, $newJours);

        $this->jourRepository->flush();
        $this->plaineJourRepository->flush();
        $this->plaineRepository->flush();
    }

    private function getJourEntityByJour(Jour $jour): Jour
    {
        if ($jour->getId()) {
            return $jour;
        }

        if (!$newJour = $this->jourRepository->findOneByDateJour($jour->getDateJour())) {
            $newJour = new Jour($jour->getDateJour());
            $this->jourRepository->persist($newJour);
        }

        return $newJour;
    }

    private function getPlaineJourByPlaineAndJour(Plaine $plaine, Jour $jour): PlaineJour
    {
        if (!$jour->getId()) {
            $plaineJour = new PlaineJour($plaine, $jour);
            $this->plaineJourRepository->persist($plaineJour);

            return $plaineJour;
        }

        if ($plaineJour = $this->plaineJourRepository->findByPlaineAndJour($plaine, $jour)) {
            return $plaineJour;
        }

        $plaineJour = new PlaineJour($plaine, $jour);
        $this->plaineJourRepository->persist($plaineJour);

        return $plaineJour;
    }

    /**
     * Ajoute au moins deux dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $plaine->initJours();
        $currentJours = $this->findPlaineJoursByPlaine($plaine);
        if (count($currentJours) == 0) {
            $today = new Jour(new \DateTime('today'));
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
     */
    private function removePlaineJours(Plaine $plaine, iterable $newJours)
    {
        $currentPlaineJours = $this->findPlaineJoursByPlaine($plaine);

        foreach ($currentPlaineJours as $plaineJour) {
            $found = false;
            $jourEntity = $plaineJour->getJour();
            foreach ($newJours as $newJour) {
                if ($jourEntity->getDateJour()->format('Y-m-d') == $newJour->getDateJour()->format('Y-m-d')) {
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                $this->jourRepository->remove($jourEntity);
                $this->plaineJourRepository->remove($plaineJour);
            }
        }
    }
}
