<?php

namespace AcMarche\Mercredi\Plaine\Dto;

use AcMarche\Mercredi\Entity\Jour;

class PlaineGroupingByDayDto
{
    public function __construct(public Jour $jour, public array $enfants, public array $groupes)
    {
    }

}