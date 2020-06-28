<?php


namespace AcMarche\Mercredi\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;

trait PedagogiqueTrait
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $pedagogique = false;

    /**
     * @return bool
     */
    public function isPedagogique(): bool
    {
        return $this->pedagogique;
    }

    /**
     * @param bool $pedagogique
     */
    public function setPedagogique(bool $pedagogique): void
    {
        $this->pedagogique = $pedagogique;
    }

}
