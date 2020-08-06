<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Accueil.
 *
 * @ORM\Table("accueil", uniqueConstraints={
 * @ORM\UniqueConstraint(columns={"date_jour", "enfant_id", "heure"})
 * })
 * @ORM\Entity()
 * @UniqueEntity(fields={"date_jour", "enfant", "heure"}, message="L'enfant est déjà inscrit à cette date")
 */
class Accueil implements TimestampableInterface, UuidableInterface
{
    use TimestampableTrait;
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use RemarqueTrait;
    use UserAddTrait;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date")
     * @Assert\Type("datetime")
     */
    private $dateTime;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $duree;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $heure;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Enfant
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Tuteur
     */
    private $tuteur;

    public function __construct(Tuteur $tuteur, Enfant $enfant)
    {
        $this->enfant = $enfant;
        $this->tuteur = $tuteur;
        $this->duree = 0;
    }

    public function __toString(): string
    {
        return $this->dateTime->format('Y-m-d');
    }

    public function getDateJour(): ?DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getHeure(): string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }
}
