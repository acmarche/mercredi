<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureHandlerInterface
{
    public function newFacture(Tuteur $tuteur): FactureInterface;

    public function handleManually(FactureInterface $facture, array $presencesId, array $accueilsId): FactureInterface;

    public function generateByMonthForTuteur(Tuteur $tuteur, string $month): ?FactureInterface;

    /**
     * @return array|FactureInterface[]
     */
    public function generateByMonthForEveryone(string $monthSelected): array;

    public function isBilled(int $presenceId, string $type): bool;
    public function isSended(int $presenceId, string $type): bool;

    public function registerDataOnFacturePresence(
        FactureInterface $facture,
        PresenceInterface $presence,
        FacturePresence $facturePresence
    ): void;
}
