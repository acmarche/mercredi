<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

use Doctrine\ORM\Mapping as ORM;

trait LastLoginTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTime $last_login = null;

    public function getLastLogin(): ?\DateTime
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTime $last_login): void
    {
        $this->last_login = $last_login;
    }

}
