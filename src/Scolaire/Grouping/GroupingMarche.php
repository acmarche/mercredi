<?php

namespace AcMarche\Mercredi\Scolaire\Grouping;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Utils\SortUtils;

class GroupingMarche implements GroupingInterface
{
    private ScolaireUtils $scolaireUtils;

    public function __construct(ScolaireUtils $scolaireUtils)
    {
        $this->scolaireUtils = $scolaireUtils;
    }

    public function groupEnfantsForPresence(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);
            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        $groups = SortUtils::sortGroupesScolairesByOrder($groups);

        return $groups;
    }

    /**
     * @param Plaine $plaine
     * @param array|Enfant[] $enfants
     * @return array|Enfant[]
     */
    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array
    {
        $groups = [];
        $jour = $plaine->getFirstDay();
        $date = $jour->getDateJour();
        foreach ($enfants as $enfant) {
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAge($enfant->getAge($date, true));
            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        $groups = SortUtils::sortGroupesScolairesByOrder($groups);

        return $groups;
    }
}
