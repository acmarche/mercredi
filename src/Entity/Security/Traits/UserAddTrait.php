<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UserAddTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private $userAdd;

    public function getUserAdd(): ?string
    {
        return $this->userAdd;
    }

    public function setUserAdd(?string $userAdd): void
    {
        $this->userAdd = $userAdd;
    }
}
