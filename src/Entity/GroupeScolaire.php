<?php


namespace AcMarche\Mercredi\Entity;


use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository")
 */
class GroupeScolaire
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var int
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $age_minimum;
    /**
     * @var int
     * @ORM\Column(type="integer", length=10, nullable=false)
     */
    private $age_maximum;

    /**
     * @var Enfant[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Enfant", mappedBy="groupe_scolaire")
     */
    private $enfants;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getAgeMinimum(): ?int
    {
        return $this->age_minimum;
    }

    public function setAgeMinimum(int $age_minimum): self
    {
        $this->age_minimum = $age_minimum;

        return $this;
    }

    public function getAgeMaximum(): ?int
    {
        return $this->age_maximum;
    }

    public function setAgeMaximum(int $age_maximum): self
    {
        $this->age_maximum = $age_maximum;

        return $this;
    }

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setGroupeScolaire($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            // set the owning side to null (unless already changed)
            if ($enfant->getGroupeScolaire() === $this) {
                $enfant->setGroupeScolaire(null);
            }
        }

        return $this;
    }
}
