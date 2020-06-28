<?php


namespace AcMarche\Mercredi\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;

trait HalfTrait
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $half;

    /**
     * @return bool
     */
    public function isHalf(): bool
    {
        return $this->half;
    }

    /**
     * @param bool $half
     */
    public function setHalf(bool $half): void
    {
        $this->half = $half;
    }


}
