<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UserNameTrait
{
    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @var string
     */
    private $username;

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
