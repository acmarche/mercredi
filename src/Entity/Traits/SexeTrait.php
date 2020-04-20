<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait SexeTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $sexe;

    /**
     * @return string|null
     */
    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    /**
     * @param string|null $sexe
     */
    public function setSexe(?string $sexe): void
    {
        $this->sexe = $sexe;
    }
}
