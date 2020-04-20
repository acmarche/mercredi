<?php


namespace AcMarche\Mercredi\Entity\Traits;


trait UserAddTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=false)
     */
    private $userAdd;

    /**
     * @return string|null
     */
    public function getUserAdd(): ?string
    {
        return $this->userAdd;
    }

    /**
     * @param string|null $userAdd
     */
    public function setUserAdd(?string $userAdd): void
    {
        $this->userAdd = $userAdd;
    }

}
