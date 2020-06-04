<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait EnabledTrait
{
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
