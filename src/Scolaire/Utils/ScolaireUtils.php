<?php


namespace AcMarche\Mercredi\Scolaire\Utils;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;

class ScolaireUtils
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
        if ($groupeScolaire = $enfant->getGroupeScolaire()) {
            return $groupeScolaire;
        }

        $annee_scolaire = $enfant->getAnneeScolaire();

        if ($groupeScolaire = $this->groupeScolaireRepository->findByAnneeScolaire($annee_scolaire)) {
            return $groupeScolaire;
        }
        $groupes = $this->groupeScolaireRepository->findAll();

        return $groupes[0];
    }
}
