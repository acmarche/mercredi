<?php

namespace AcMarche\Mercredi\Scolaire\Grouping;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Utils\SortUtils;

class GroupingMarche implements GroupingInterface
{
    public function __construct(
        private ScolaireUtils $scolaireUtils,
        private PlaineGroupeRepository $plaineGroupeRepository
    ) {
    }

    public function groupEnfantsForPresence(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groupe = $this->findGroupeScolaire($enfant);
            if (!$groupe) {
                continue;
            }
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
        return $this->groupEnfantsForPresence($enfants);
    }

    public function setEnfantsForGroupesScolaire(Plaine $plaine, array $enfants)
    {
        foreach ($this->plaineGroupeRepository->findByPlaine($plaine) as $plaineGroupe) {
            $goupeScolaireId = $plaineGroupe->getGroupeScolaire()->getId();
            foreach ($enfants as $enfant) {
                $groupe = $this->findGroupeScolaire($enfant);
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
        return null;
    }

    public function findGroupeScolaire(Enfant $enfant, Plaine $plaine = null): ?GroupeScolaire
    {
        return $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);
    }
}
