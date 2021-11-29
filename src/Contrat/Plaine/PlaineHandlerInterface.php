<?php

namespace AcMarche\Mercredi\Contrat\Plaine;

use Exception;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\Collection;

interface PlaineHandlerInterface
{
    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): void;

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $collection
    ): void;

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void;

    public function isRegistrationFinalized(Plaine $plaine, Tuteur $tuteur): bool;

    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @throws Exception
     */
    public function confirm(Plaine $plaine, Tuteur $tuteur): void;
}
