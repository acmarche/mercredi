<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AdresseTrait
{
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $rue = null;

    #[ORM\Column(type: 'integer', length: 6, nullable: true)]
    private ?int $code_postal = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $localite = null;

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): void
    {
        $this->rue = $rue;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(?int $code_postal): void
    {
        $this->code_postal = $code_postal;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): void
    {
        $this->localite = $localite;
    }
}
