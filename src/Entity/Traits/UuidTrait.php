<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
    protected ?string $uuid = null;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function generateUuid(): string
    {
        return Uuid::v4();
    }
}
