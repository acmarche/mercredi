<?php

namespace AcMarche\Mercredi\Entity\Security;

use AcMarche\Mercredi\Security\Token\TokenRepository;
use Doctrine\DBAL\Types\Types;
use Stringable;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
class Token implements TimestampableInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    #[Assert\NotBlank]
    protected string $value;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    protected DateTimeInterface $expire_at;

    #[ORM\OneToOne(targetEntity: User::class)]
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getExpireAt(): DateTimeInterface
    {
        return $this->expire_at;
    }

    /**
     * @param DateTime $expire_at
     */
    public function setExpireAt(DateTimeInterface $expire_at): void
    {
        $this->expire_at = $expire_at;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
