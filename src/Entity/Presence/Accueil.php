<?php

namespace AcMarche\Mercredi\Entity\Presence;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\RetardTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: 'accueil')]
#[ORM\UniqueConstraint(columns: ['date_jour', 'enfant_id', 'heure'])]
#[ORM\Entity(repositoryClass: AccueilRepository::class)]
#[UniqueEntity(fields: ['date_jour', 'enfant', 'heure'], message: "L'enfant est déjà inscrit à cette date")]
class Accueil implements TimestampableInterface, UuidableInterface, Stringable
{
    use TimestampableTrait;
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use RemarqueTrait;
    use UserAddTrait;
    use RetardTrait;

    #[ORM\ManyToOne(targetEntity: Tuteur::class, inversedBy: 'accueils')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tuteur $tuteur = null;

    #[ORM\ManyToOne(targetEntity: Enfant::class, inversedBy: 'accueils')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enfant $enfant = null;

    #[ORM\Column(type: 'date')]
    #[Assert\Type(type: 'datetime')]
    private ?DateTimeInterface $date_jour = null;
    #[ORM\Column(type: 'smallint')]
    private int $duree;
    #[ORM\Column(type: 'string', nullable: false, length: 50)]
    private ?string $heure = null;

    public float $cout = 0;//pour attestation

    public function __construct(
        Tuteur $tuteur,
        Enfant $enfant
    ) {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
        $this->duree = 0;
    }

    public function __toString(): string
    {
        return $this->date_jour->format('Y-m-d');
    }

    public function getDateJour(): ?\DateTimeInterface
    {
        return $this->date_jour;
    }

    public function setDateJour(\DateTimeInterface $date_jour): self
    {
        $this->date_jour = $date_jour;

        return $this;
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

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }
}
