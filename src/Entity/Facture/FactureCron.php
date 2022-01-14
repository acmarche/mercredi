<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Facture\Repository\FactureCronRepository')]
class FactureCron implements TimestampableInterface
{
    use IdTrait;
    use UserAddTrait;
    use TimestampableTrait;
    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $month;
    #[ORM\Column(type: 'string', length: 150, nullable: false)]
    private string $subject;
    #[ORM\Column(type: 'text', nullable: false)]
    private string $body;
    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $fromAdresse;
    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $done = false;

    public function __construct(string $from, string $subject, string $body, string $month)
    {
        $this->fromAdresse = $from;
        $this->subject = $subject;
        $this->body = $body;
        $this->month = $month;
    }

    public function getMonth(): string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getFromAdresse(): string
    {
        return $this->fromAdresse;
    }

    public function setFromAdresse(string $fromAdresse): self
    {
        $this->fromAdresse = $fromAdresse;

        return $this;
    }

    public function getDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }
}
