<?php

namespace AcMarche\Mercredi\Entity\Security;

use AcMarche\Mercredi\Entity\Security\Traits\IsRoleTrait;
use AcMarche\Mercredi\Entity\Security\Traits\PlainPasswordTrait;
use AcMarche\Mercredi\Entity\Security\Traits\RoleTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UserNameTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\EnabledTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\TuteursTrait;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    use IdTrait;
    use EmailTrait;
    use NomTrait;
    use PrenomTrait;
    use RoleTrait;
    use EnabledTrait;
    use PlainPasswordTrait;
    use IsRoleTrait;
    use UserNameTrait;
    use TuteursTrait;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    public function __construct()
    {
        $this->tuteurs = new ArrayCollection();
    }

    public function __toString()
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
