<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ColorTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Jour\Repository\JourRepository")
 * @UniqueEntity("date_jour")
 */
class Jour implements TimestampableInterface
{
    use IdTrait;

    use TimestampableTrait;

    use PrixTrait;

    use ColorTrait;

    use RemarqueTrait;

    use ArchiveTrait;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date", unique=true)
     * @Assert\Type("datetime")
     */
    private $date_jour;

    public function __construct()
    {
        $this->prix1 = 0.0;
        $this->prix2 = 0.0;
        $this->prix3 = 0.0;
    }

    public function __toString()
    {
        return $this->date_jour->format('d-m-Y');
    }

    public function getDateJour(): ?\DateTime
    {
        return $this->date_jour;
    }

    public function setDateJour(?\DateTime $date_jour): void
    {
        $this->date_jour = $date_jour;
    }
}
