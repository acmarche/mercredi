<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait NomTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }
}
