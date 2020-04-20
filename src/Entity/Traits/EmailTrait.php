<?php


namespace AcMarche\Mercredi\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;


trait EmailTrait
{
    /**
     * @var string|null
     * @Assert\Email()
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

}
