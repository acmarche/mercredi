<?php

namespace AcMarche\Mercredi\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RetardTrait
{
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("datetime")
     */
    private ?DateTimeInterface $date_retard = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("datetime")
     */
    private ?DateTimeInterface $heure_retard = null;

    public function getDateRetard(): ?DateTimeInterface
    {
        return $this->date_retard;
    }

    public function setDateRetard(?DateTimeInterface $date_retard): void
    {
        $this->date_retard = $date_retard;
    }

    public function getHeureRetard(): ?DateTimeInterface
    {
        return $this->heure_retard;
    }

    public function setHeureRetard(?DateTimeInterface $heure_retard): void
    {
        $this->heure_retard = $heure_retard;
    }

}
