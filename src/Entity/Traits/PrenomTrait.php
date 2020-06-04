<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait PrenomTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $prenom;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
}
