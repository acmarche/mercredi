<?php


namespace AcMarche\Mercredi\Entity;


use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Accueil
 * * @ORM\Table("accueil", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"date_jour", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Accueil\Repository\AccueilRepository")
 * @UniqueEntity(fields={"date_jour", "enfant"}, message="L'enfant est déjà inscrit à cette date")
 */
class Accueil implements TimestampableInterface
{
    use TimestampableTrait;
    use IdTrait;
    use EnfantTrait;
    use TuteurTrait;
    use RemarqueTrait;
    use UserAddTrait;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date")
     * @Assert\Type("datetime")
     */
    private $date_jour;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $duree;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $matin_soir = [];

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tuteur;

    public function __construct(Tuteur $tuteur, Enfant $enfant)
    {
        $this->enfant = $enfant;
        $this->tuteur = $tuteur;
    }

    public function __toString()
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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getMatinSoir(): ?array
    {
        return $this->matin_soir;
    }

    public function setMatinSoir(array $matin_soir): self
    {
        $this->matin_soir = $matin_soir;

        return $this;
    }

    public function addMatinSoir(string $matin_soir): void
    {
        if (!\in_array($matin_soir, $this->matin_soir, true)) {
            $this->matin_soir[] = $matin_soir;
        }
    }

    public function removeMatinSoir(string $matin_soir): void
    {
        if (\in_array($matin_soir, $this->matin_soir, true)) {
            $index = array_search($matin_soir, $this->matin_soir);
            unset($this->matin_soir[$index]);
        }
    }


}
