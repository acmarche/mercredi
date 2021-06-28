<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

trait PlainPasswordTrait
{
    private ?string $plain_password;

    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    public function setPlainPassword(?string $plain_password): void
    {
        $this->plain_password = $plain_password;
    }
}
