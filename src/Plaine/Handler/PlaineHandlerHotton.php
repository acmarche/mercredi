<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\Collection;
use Exception;

class PlaineHandlerHotton implements PlaineHandlerInterface
{
    public function __construct()
    {
    }

    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant, iterable $jours = []): array
    {
        return [];
    }

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $newJours
    ): void {

    }

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void
    {

    }

    public function isRegistrationFinalized(Plaine $plaine, Tuteur $tuteur): bool
    {
        return false;
    }

    /**
     * @throws Exception
     */
    public function confirm(Plaine $plaine, Tuteur $tuteur): void
    {

    }
}
