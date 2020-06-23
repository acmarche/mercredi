<?php

namespace AcMarche\Mercredi\Entity\Sante\Traits;

trait FicheSanteIsCompleteTrait
{
    /**
     * @var bool
     */
    private $ficheSanteIsComplete = false;

    public function isFicheSanteIsComplete(): bool
    {
        return $this->ficheSanteIsComplete;
    }

    public function setFicheSanteIsComplete(bool $ficheSanteIsComplete): void
    {
        $this->ficheSanteIsComplete = $ficheSanteIsComplete;
    }
}
