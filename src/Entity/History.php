<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Spam\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    use IdTrait;

    #[ORM\Column(length: 50, nullable: false)]
    public string $subject;
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $message = null;
    #[ORM\Column(type: 'date', nullable: false)]
    public ?\DateTime $created_at = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $count = 0;

    public function __construct(string $subject)
    {
        $this->subject = $subject;
    }

}