<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Entity\Enfant;

trait EnfantTrait
{
    /**
     * @var Enfant
     */
    protected $enfant;

    /**
     * @return Enfant
     */
    public function getEnfant(): Enfant
    {
        return $this->enfant;
    }

    /**
     * @param Enfant $enfant
     */
    public function setEnfant(Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }

}
