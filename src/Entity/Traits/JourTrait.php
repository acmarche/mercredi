<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Entity\Jour;

trait JourTrait
{
    /**
     * @var Jour
     *
     * */
    protected $jour;

    /**
     * @return Jour
     */
    public function getJour(): Jour
    {
        return $this->jour;
    }

    /**
     * @param Jour $jour
     */
    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }

}
