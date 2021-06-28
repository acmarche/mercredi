<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Enfant;

trait EnfantTrait
{
    /**
     * @var Enfant
     */
    private ?Enfant $enfant = null;

    public function getEnfant(): Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }
}
