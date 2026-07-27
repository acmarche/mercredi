<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AcceptMigrationTrait
{
    #[ORM\Column(name: 'accept_migration', type: 'boolean', nullable: false)]
    private bool $acceptMigration = false;

    public function isAcceptMigration(): bool
    {
        return $this->acceptMigration;
    }

    public function setAcceptMigration(bool $acceptMigration): void
    {
        $this->acceptMigration = $acceptMigration;
    }
}
