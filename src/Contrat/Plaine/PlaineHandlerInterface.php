<?php

namespace AcMarche\Mercredi\Contrat\Plaine;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\Collection;
use Exception;

interface PlaineHandlerInterface
{
    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @param Enfant $enfant
     * @param iterable $jours
     * @return array|Jour[]
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws Exception
     */
    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant, iterable $jours = []): array;

    /**
     * @param Plaine $plaine
     * @param Tuteur $tuteur
     * @param Enfant $enfant
     * @param array $currentJours
     * @param Collection $newJours
     * @return array|Jour[]
     * @throws Exception
     */
    public function handleEditPresences(
        Plaine $plaine,
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $newJours
    ): array;

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void;

    public function isRegistrationFinalized(Plaine $plaine, Tuteur $tuteur): bool;

    /**
     * @throws Exception
     */
    public function confirm(Plaine $plaine, Tuteur $tuteur): void;

    /**
     * @param Enfant $enfant
     * @return GroupeScolaire|null
     */
    public function getGroupeScolaire(Enfant $enfant): ?GroupeScolaire;

    /**
     * @param Plaine $plaine
     * @param GroupeScolaire $groupeScolaire
     * @return PlaineGroupe|null
     */
    public function getPlaineGroupe(Plaine $plaine, GroupeScolaire $groupeScolaire): ?PlaineGroupe;
}
