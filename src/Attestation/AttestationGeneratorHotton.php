<?php

namespace AcMarche\Mercredi\Attestation;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Attestation\AttestationGeneratorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Twig\Environment;

class AttestationGeneratorHotton implements AttestationGeneratorInterface
{
    use OrganisationPropertyInitTrait;

    public function __construct(
        private PresenceRepository $presenceRepository,
        private FactureRepository $factureRepository,
        private AccueilRepository $accueilRepository,
        private PresenceCalculatorInterface $presenceCalculator,
        private FactureCalculatorInterface $factureCalculator,
        private FactureUtils $factureUtils,
        private Environment $environment,
    ) {}

    public function hasAttestation(Tuteur $tuteur, Enfant $enfant, int $year): bool
    {
        $presences = $this->presenceRepository->findByTuteurAndEnfantAndYear($tuteur, $enfant, $year);
        $accueils = $this->accueilRepository->findByTuteurAndEnfantAndYear($tuteur, $enfant, $year);

        return count($presences) === 0 && count($accueils) === 0;
    }

    public function renderOne(Tuteur $tuteur, Enfant $enfant, int $year): string
    {
        $factures = $this->factureRepository->findByTuteurAndPaidInYear($tuteur, $year);

        $data = $this->treatmentForTuteur($factures, $tuteur, $enfant);

        return $this->environment->render('@AcMarcheMercredi/admin/attestation/one/hotton/2022.html.twig', [
            'data' => $data,
            'tuteur' => $tuteur,
            'enfant' => $enfant,
            'year' => $year,
            'today' => new \DateTime(),
            'organisation' => $this->organisation,
        ]);
    }

    public function getDataByYear(int $year): array
    {
        $presences = $this->presenceRepository->findByYear($year);

        return $this->treatment($presences);
    }

    private function treatment(array $presences): array
    {
        $presencesPaid = [];
        foreach ($presences as $presence) {
            if ($this->factureCalculator->isPresencePaid($presence)) {
                $presence->cout = $this->presenceCalculator->calculate($presence);
                $presencesPaid[] = $presence;
            }
        }

        $data = [];
        foreach ($presencesPaid as $presence) {
            $enfant = $presence->getEnfant();
            $idEnfant = $enfant->getId();
            $tuteur = $presence->getTuteur();
            $idTuteur = $tuteur->getId();

            $data[$idEnfant]['enfant'] = $enfant;
            $data[$idEnfant]['tuteurs'][$idTuteur]['tuteur'] = $tuteur;
            $data[$idEnfant]['tuteurs'][$idTuteur]['presences'][] = $presence;
            if (!isset($data[$idEnfant]['tuteurs'][$idTuteur]['total'])) {
                $data[$idEnfant]['tuteurs'][$idTuteur]['total'] = 0;
            }
            $data[$idEnfant]['tuteurs'][$idTuteur]['total'] += $presence->cout;
        }

        return $data;
    }

    /**
     * @param Facture[] $factures
     * @param Tuteur $tuteur
     * @param Enfant $enfant
     * @return array
     */
    private function treatmentForTuteur(array $factures, Tuteur $tuteur, Enfant $enfant): array
    {
        $presencesPaid = [];
        foreach ($factures as $facture) {
            foreach ($facture->getFacturePresences() as $facturePresence) {
                if ($facturePresence->getObjectType() == Facture::OBJECT_ACCUEIL) {
                    if ($accueil = $this->accueilRepository->find($facturePresence->getPresenceId())) {
                        if ($accueil->getEnfant()->getId() == $enfant->getId()) {
                            $accueil->cout = $facturePresence->getCoutCalculated();
                            $presencesPaid[] = $accueil;
                        }
                    }
                }
                if ($facturePresence->getObjectType() == Facture::OBJECT_PRESENCE) {
                    if ($presence = $this->presenceRepository->find($facturePresence->getPresenceId())) {
                        if ($presence->getEnfant()->getId() == $enfant->getId()) {
                            $presence->cout = $facturePresence->getCoutCalculated();
                            $presencesPaid[] = $presence;
                        }
                    }
                }
            }
        }
        $data = [];
        foreach ($presencesPaid as $presence) {
            $idEnfant = $enfant->getId();
            $idTuteur = $tuteur->getId();
            $data[$idEnfant]['enfant'] = $enfant;
            $data[$idEnfant]['tuteurs'][$idTuteur]['tuteur'] = $tuteur;
            $data[$idEnfant]['tuteurs'][$idTuteur]['presences'][] = $presence;
            if (!isset($data[$idEnfant]['tuteurs'][$idTuteur]['total'])) {
                $data[$idEnfant]['tuteurs'][$idTuteur]['total'] = 0;
            }
            $data[$idEnfant]['tuteurs'][$idTuteur]['total'] += $presence->cout;
        }

        return $data;
    }
}