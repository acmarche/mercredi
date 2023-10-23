<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\ResetPassword\Repository\ResetPasswordRequestRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'request_password')]
    #[ORM\JoinColumn(nullable: false)] private object $user;

    public function __construct(
        User $user,
        DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ) {
        $this->initialize($expiresAt, $selector, $hashedToken);
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): object
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
