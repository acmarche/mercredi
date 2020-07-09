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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Accueil
 * @package AcMarche\Mercredi\Entity
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Accueil\Repository\AccueilRepository")
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
     * @ORM\Column(name="date_jour", type="date", unique=true)
     * @Assert\Type("datetime")
     */
    private $date_jour;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    private $nb_demi_heure;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $matin_apres_midi = [];

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant")
     */
    private $enfant;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur")
     */
    private $tuteur;

    public function __construct(Enfant $enfant)
    {
        $this->enfant = $enfant;
        //$this->tuteur = $tuteur;
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

    public function getNbDemiHeure(): ?int
    {
        return $this->nb_demi_heure;
    }

    public function setNbDemiHeure(int $nb_demi_heure): self
    {
        $this->nb_demi_heure = $nb_demi_heure;

        return $this;
    }

    public function getMatinApresMidi(): ?array
    {
        return $this->matin_apres_midi;
    }

    public function setMatinApresMidi(array $matin_apres_midi): self
    {
        $this->matin_apres_midi = $matin_apres_midi;

        return $this;
    }

    public function addMatinApresMidi(string $role): void
    {
        if (!\in_array($role, $this->matin_apres_midi, true)) {
            $this->matin_apres_midi[] = $role;
        }
    }

    public function removeMatinApresMidi(string $matin_apres_midi): void
    {
        if (\in_array($matin_apres_midi, $this->matin_apres_midi, true)) {
            $index = array_search($matin_apres_midi, $this->matin_apres_midi);
            unset($this->matin_apres_midi[$index]);
        }
    }


}
