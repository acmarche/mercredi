<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait ColorTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $color;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
