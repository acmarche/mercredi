<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Spam\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ORM\UniqueConstraint(columns: ['subject', 'created_at'])]
#[UniqueEntity(fields: ['subject', 'created_at'], message: 'sujet unique')]
class History
{
    use IdTrait;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    public string $subject;
    #[ORM\Column(type: 'date', nullable: false)]
    public ?\DateTime $created_at = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $count = 0;

    public function __construct(string $subject)
    {
        $this->subject = $subject;
    }

    public function addCount(): void
    {
        $this->count = $this->count + 1;
    }

}