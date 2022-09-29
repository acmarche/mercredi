<?php

namespace AcMarche\Mercredi\Scolaire\Grouping;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Utils\SortUtils;

class GroupingMarche implements GroupingInterface
{
    public function __construct(
        private ScolaireUtils $scolaireUtils,
        private PlaineGroupeRepository $plaineGroupeRepository,
        private GroupeScolaireRepository $groupeScolaireRepository
    ) {
    }

    public function groupEnfantsForPresence(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);
            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        return SortUtils::sortGroupesScolairesByOrder($groups);
    }

    /**
     * @param array|Enfant[] $enfants
     *
     * @return array|Enfant[]
     */
    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array
    {
        $groups = [];
        $jour = $plaine->getFirstDay();
        $date = $jour->getDateJour();
        $groupeForce = new GroupeScolaire();
        $groupeForce->setNom('Age non determine');
        $groupeForce->id = 99999;
        foreach ($enfants as $enfant) {
            $groupe = $this->findGroupeScolaireByAge($enfant->getAge($date, true));
            if (null === $groupe) {
                $groupe = $groupeForce;
            }
            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        return SortUtils::sortGroupesScolairesByOrder($groups);
    }

    public function setEnfantsByGroupeScolaire(Plaine $plaine, array $enfants)
    {
        $jour = $plaine->getFirstDay();
        $date = $jour->getDateJour();

        foreach ($this->plaineGroupeRepository->findByPlaine($plaine) as $plaineGroupe) {
            $goupeScolaireId = $plaineGroupe->getGroupeScolaire()->getId();
            foreach ($enfants as $enfant) {
                $groupe = $this->findGroupeScolaireByAge($enfant->getAge($date, true));
                if (null === $groupe) {
                    continue;
                }
                if ($groupe->getId() === $goupeScolaireId) {
                    $plaineGroupe->enfants[] = $enfant;
                }
            }
        }
    }

    public function findGroupeScolaireByAge(float $age): ?GroupeScolaire
    {
        return $this->groupeScolaireRepository->findGroupePlaineByAge($age);
    }

}
