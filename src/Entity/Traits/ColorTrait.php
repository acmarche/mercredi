<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait ColorTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $color;

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

}
