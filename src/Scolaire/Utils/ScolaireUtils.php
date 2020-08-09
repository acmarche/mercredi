<?php

namespace AcMarche\Mercredi\Scolaire\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;

final class ScolaireUtils
{
    /**
     * @var GroupeScolaireRepository
     */
    private $groupeScolaireRepository;

    public function __construct(GroupeScolaireRepository $groupeScolaireRepository)
    {
        $this->groupeScolaireRepository = $groupeScolaireRepository;
    }

    public function findGroupeScolaireEnfantByAnneeScolaire(Enfant $enfant): GroupeScolaire
    {
        if (null !== ($groupeScolaire = $enfant->getGroupeScolaire())) {
            return $groupeScolaire;
        }

        $anneeScolaire = $enfant->getAnneeScolaire();

        if ($groupeScolaire = $anneeScolaire->getGroupeScolaire()) {
            return $groupeScolaire;
        }

        $groupes = $this->groupeScolaireRepository->findAll();

        return $groupes[0];
    }
}
