<?php

namespace AcMarche\Mercredi\Entity\Security;

use AcMarche\Mercredi\Entity\ResetPasswordRequest;
use AcMarche\Mercredi\Entity\Security\Traits\AnimateursTrait;
use AcMarche\Mercredi\Entity\Security\Traits\IsRoleTrait;
use AcMarche\Mercredi\Entity\Security\Traits\LastLoginTrait;
use AcMarche\Mercredi\Entity\Security\Traits\PlainPasswordTrait;
use AcMarche\Mercredi\Entity\Security\Traits\RoleTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UserNameTrait;
use AcMarche\Mercredi\Entity\Traits\EcolesTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\EnabledTrait;
use AcMarche\Mercredi\Entity\Traits\IdOldTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\TuteursTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: 'email')]
#[UniqueEntity(fields: 'username')]
class User implements UserInterface, LegacyPasswordAuthenticatedUserInterface, Stringable, TimestampableInterface
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
    use EcolesTrait;
    use AnimateursTrait;
    use LastLoginTrait;
    use IdOldTrait;
    use TimestampableTrait;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $salt = null;
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $telephone = null;
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $email = null;
    /**
     * The hashed password.
     */
    #[ORM\Column(type: 'string')]
    private ?string $password = null;
    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;
    #[ORM\OneToMany(targetEntity: ResetPasswordRequest::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $request_password;

    public function __construct()
    {
        $this->tuteurs = new ArrayCollection();
        $this->ecoles = new ArrayCollection();
        $this->animateurs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }

    /**
     * @param object|Tuteur| $object
     */
    public static function newFromObject(object $object): self
    {
        $user = new self();
        $user->setNom($object->getNom());
        $user->setPrenom($object->getPrenom());
        $user->setEmail($object->getEmail());
        $user->setTelephone($object->getTelephone());

        return $user;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getNiceRoles(): array
    {
        return MercrediSecurityRole::niceName($this->getRoles());
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }
}
