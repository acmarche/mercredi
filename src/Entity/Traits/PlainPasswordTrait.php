<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait PlainPasswordTrait
{
    /**
     * @var string|null
     */
    private $plain_password;

    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    public function setPlainPassword(?string $plain_password): void
    {
        $this->plain_password = $plain_password;
    }
}
