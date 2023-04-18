<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
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
        Plaine $plaine,
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $newJours
    ): array {
        return [];
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
    public function getGroupeScolaire(Enfant $enfant): ?GroupeScolaire
    {
        return null;
    }

    public function getPlaineGroupe(Plaine $plaine, GroupeScolaire $groupeScolaire): ?PlaineGroupe
    {
        return null;
    }

}
