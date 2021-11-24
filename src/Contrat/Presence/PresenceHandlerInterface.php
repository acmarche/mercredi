<?php

namespace AcMarche\Mercredi\Contrat\Presence;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\ORM\NonUniqueResultException;

interface PresenceHandlerInterface
{
    /**
     * @param Jour[] $days
     *
     * @throws NonUniqueResultException
     */
    public function handleNew(Tuteur $tuteur, Enfant $enfant, iterable $days): void;

    public function searchAndGrouping(Jour $jour, ?Ecole $ecole, bool $displayRemarque): array;

    public function checkConstraints(Jour $jour): bool;
}
