<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait InscriptionOpenTrait
{
    /**
     * @ORM\Column(type="boolean")
     * Assert\Type() //todo my constraint only one
     */
    private bool $inscriptionOpen = false;

    public function isInscriptionOpen(): bool
    {
        return $this->inscriptionOpen;
    }

    public function setInscriptionOpen(bool $inscriptionOpen): void
    {
        $this->inscriptionOpen = $inscriptionOpen;
    }
}
