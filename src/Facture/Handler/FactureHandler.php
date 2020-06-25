<?php

namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class FactureHandler
{
    /**
     * @var FactureRepository
     */
    private $factureRepository;
    /**
     * @var FactureFactory
     */
    private $factureFactory;
    /**
     * @var PresenceCalculatorInterface
     */
    private $presenceCalculator;
    /**
     * @var FacturePresenceRepository
     */
    private $facturePresenceRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureFactory $factureFactory,
        PresenceCalculatorInterface $presenceCalculator,
        PresenceRepository $presenceRepository
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureFactory = $factureFactory;
        $this->presenceCalculator = $presenceCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->presenceRepository = $presenceRepository;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param int[] $presencesId
     */
    public function handleNew(Facture $facture, array $presencesId, bool $persist = true): Facture
    {
        foreach ($presencesId as $presenceId) {
            if (!$presence = $this->presenceRepository->find($presenceId)) {
                continue;
            }
            if ($this->facturePresenceRepository->findByPresence($presence)) {
                continue;
            }
            $facturePresence = new FacturePresence($facture, $presence);
            $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
            $enfant = $presence->getEnfant();
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $facturePresence->setCout($this->presenceCalculator->calculate($presence));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
        if ($persist) {
            $this->factureRepository->persist($facture);
        }
        $this->factureRepository->flush();
        $this->facturePresenceRepository->flush();

        return $facture;
    }

}
