<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Jour;

trait JourTrait
{
    /**
     * @var Jour
     *
     * */
    private ?Jour $jour = null;

    public function getJour(): Jour
    {
        return $this->jour;
    }

    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }
}
