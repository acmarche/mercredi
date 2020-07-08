<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ContentTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
