<?php


namespace AcMarche\Mercredi\Entity\Plaine;


trait PlaineTrait
{
    /**
     * @var Plaine|null
     */
    private $plaine;

    /**
     * @return Plaine|null
     */
    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    /**
     * @param Plaine|null $plaine
     */
    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }


}
