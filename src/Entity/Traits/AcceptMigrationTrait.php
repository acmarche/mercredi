<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AcceptMigrationTrait
{
    /**
     * null tant que le parent n'a pas répondu, true s'il accepte, false s'il refuse.
     */
    #[ORM\Column(name: 'accept_migration', type: 'boolean', nullable: true)]
    private ?bool $acceptMigration = null;

    public function isAcceptMigration(): ?bool
    {
        return $this->acceptMigration;
    }

    public function setAcceptMigration(?bool $acceptMigration): void
    {
        $this->acceptMigration = $acceptMigration;
    }
}
