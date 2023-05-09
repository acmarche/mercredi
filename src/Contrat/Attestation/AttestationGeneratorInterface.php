<?php

namespace AcMarche\Mercredi\Contrat\Attestation;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;

interface AttestationGeneratorInterface
{
    public function hasAttestation(Tuteur $tuteur, Enfant $enfant, int $year): bool;

    public function renderOne(Tuteur $tuteur, Enfant $enfant, int $year): string;

    public function getDataByYear(int $year): array;
}