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
     * @param Jour[] $jours
     */
    public function handleEditJours(Plaine $plaine, array $jours, iterable $currentJours)
    {
        $enMoins = array_diff($jours, $currentJours->toArray());
        dump($currentJours);
        dump($jours);

        foreach ($jours as $jour) {
            $date = $jour->getDateJour();
            if ($jourExistant = $this->jourRepository->findOneBy(['date_jour' => $date])) {
                $jourExistant->setPlaine($plaine);
                $plaine->removeJour($jour);
                $plaine->addJour($jourExistant);
                dump($jourExistant);
            } else {
                $jour->setPlaine($plaine);
                dump($jour);
                $this->jourRepository->persist($jour);
            }
        }

        foreach ($enMoins as $jour) {
            $plaine->removeJour($jour);
        }

        $this->jourRepository->flush();
        $this->plaineRepository->flush();
    }

    /**
     * Ajoute au moins deux dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $today = new Jour(new \DateTime());
        $today->setPlaine($plaine);
        $this->jourRepository->persist($today);
        $tomorrow = new Jour(new \DateTime('+1day'));
        $tomorrow->setPlaine($plaine);
        $this->jourRepository->persist($tomorrow);
        $plaine->addJour($today);
        $plaine->addJour($tomorrow);
    }

    /**
     * @return Jour[]
     */
    public function findJoursByPlaine(Plaine $plaine): array
    {
        return $this->jourRepository->findBy(['plaine' => $plaine]);
    }
}
