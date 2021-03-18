<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait PhotoAutorisationTrait
{
    /**
     * @var bool
     */
    private $photo_autorisation;

    public function isPhotoAutorisation(): bool
    {
        return $this->photo_autorisation;
    }

    public function setPhotoAutorisation(bool $photo_autorisation): void
    {
        $this->photo_autorisation = $photo_autorisation;
    }

    public function getPhotoAutorisation(): ?bool
    {
        return $this->photo_autorisation;
    }
}
