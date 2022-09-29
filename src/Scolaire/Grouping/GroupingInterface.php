<?php

namespace AcMarche\Mercredi\Scolaire\Grouping;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;

interface GroupingInterface
{
    public function findGroupeScolaireByAge(float $age): ?GroupeScolaire;

    public function groupEnfantsForPresence(array $enfants): array;

    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array;

    public function setEnfantsByGroupeScolaire(Plaine $plaine, array $enfants);
}
