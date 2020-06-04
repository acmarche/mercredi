<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait ArchiveTrait
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }
}
