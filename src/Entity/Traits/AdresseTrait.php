<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait AdresseTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $rue;

    /**
     * @var string|null
     *
     * @ORM\Column(type="integer", length=6, nullable=true)
     */
    private $code_postal;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $localite;

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): void
    {
        $this->rue = $rue;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): void
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
