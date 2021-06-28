<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SexeTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private ?string $sexe;

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): void
    {
        $this->sexe = $sexe;
    }
}
