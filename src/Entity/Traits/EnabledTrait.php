<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EnabledTrait
{
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
