<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use function count;

final class PlaineAdminHandler
{
    private PlaineRepository $plaineRepository;
    private JourRepository $jourRepository;
    private FactureHandlerInterface $factureHandler;
    private PlainePresenceRepository $plainePresenceRepository;

    public function __construct(
        PlaineRepository $plaineRepository,
        JourRepository $jourRepository,
        PlainePresenceRepository $plainePresenceRepository,
        FactureHandlerInterface $factureHandler
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->jourRepository = $jourRepository;
        $this->factureHandler = $factureHandler;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    /**
     * Ajoute au moins 5 dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $currentJours = $this->jourRepository->findByPlaine($plaine);
        if (0 === count($currentJours)) {
            $plaine->addJour(new Jour(new DateTime('today')));
            for ($i = 1; $i < 5; $i++) {
                $plaine->addJour(new Jour(new DateTime('+' . $i . ' day')));
            }
        }
    }

    /**
     * @param Jour[]|ArrayCollection $newJours
     */
    public function handleEditJours(Plaine $plaine, iterable $newJours): void
    {
        foreach ($newJours as $jour) {
            if ($jour->getId()) {
                continue;
            }
            $jour->setPlaine($plaine);
            $this->jourRepository->persist($jour);
        }
        $this->removeJours($plaine, $newJours);
        $this->jourRepository->flush();
    }


    /**
     * @param Jour[] $newJours
     */
    private function removeJours(Plaine $plaine, iterable $newJours): void
    {
        foreach ($this->jourRepository->findByPlaine($plaine) as $jour) {
            $found = false;
            foreach ($newJours as $newJour) {
                if ($jour->getDateJour()->format('Y-m-d') === $newJour->getDateJour()->format('Y-m-d')) {
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                if ($presences = $this->plainePresenceRepository->findByDay($jour, $plaine)) {
                    foreach ($presences as $presence) {
                        if (!$this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PLAINE)) {
                            $this->jourRepository->remove($presence);
                        }
                    }
                }
                $this->jourRepository->remove($jour);
            }
        }
    }

    public function handleOpeningRegistrations(Plaine $plaine): ?Plaine
    {
        return $this->plaineRepository->findPlaineOpen($plaine);
    }
}
