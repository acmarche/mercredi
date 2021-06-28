<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait NomTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", length=150)
     */
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}
