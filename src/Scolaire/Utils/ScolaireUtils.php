<?php

namespace AcMarche\Mercredi\Scolaire\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;

final class ScolaireUtils
{
    private GroupeScolaireRepository $groupeScolaireRepository;

    public function __construct(GroupeScolaireRepository $groupeScolaireRepository)
    {
        $this->groupeScolaireRepository = $groupeScolaireRepository;
    }

    /**
     * Retourne le groupe scolaire de l'enfant
     * Si a pas retourne le groupe scolaire de son année
     * Si a pas retourne un groupe au hasard
     * @param Enfant $enfant
     * @return GroupeScolaire
     */
    public function findGroupeScolaireEnfantByAnneeScolaire(Enfant $enfant): GroupeScolaire
    {
        if (null !== ($groupeScolaire = $enfant->getGroupeScolaire())) {
            return $groupeScolaire;
        }

        $anneeScolaire = $enfant->getAnneeScolaire();

        if (($groupeScolaire = $anneeScolaire->getGroupeScolaire()) !== null) {
            return $groupeScolaire;
        }

        $groupes = $this->groupeScolaireRepository->findGroupesNotPlaine();

        return $groupes[0];
    }

    /**
     * @param Enfant $enfant
     */
    public function findGroupeScolaireEnfantByAge(?float $age): ?GroupeScolaire
    {
        if (!$age) {
            return null;
        }

        return $this->groupeScolaireRepository->findGroupePlaineByAge($age);
    }
}
