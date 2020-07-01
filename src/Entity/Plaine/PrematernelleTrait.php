<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use Doctrine\ORM\Mapping as ORM;

trait PrematernelleTrait
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $prematernelle;

    public function getPrematernelle(): ?bool
    {
        return $this->prematernelle;
    }

    public function setPrematernelle(?bool $prematernelle): void
    {
        $this->prematernelle = $prematernelle;
    }
}
