<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait ArchiveTrait
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $archived = false;

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * @param bool $archived
     */
    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }


}
