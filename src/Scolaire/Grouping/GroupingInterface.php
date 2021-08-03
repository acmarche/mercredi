<?php

namespace AcMarche\Mercredi\Scolaire\Grouping;

use AcMarche\Mercredi\Entity\Plaine\Plaine;

interface GroupingInterface
{
    public function groupEnfantsForPresence(array $enfants): array;
    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array;
}
