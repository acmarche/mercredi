<?php


namespace AcMarche\Mercredi\Plaine\Dto;


use AcMarche\Mercredi\Entity\Plaine\Plaine;

class PlaineInscriptionDto
{
    private $plaine;

    private $enfant;

    public function __construct(Plaine $plaine)
    {
        $this->plaine = $plaine;
    }

    /**
     * @return mixed
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * @param mixed $enfant
     */
    public function setEnfant($enfant): void
    {
        $this->enfant = $enfant;
    }


}
