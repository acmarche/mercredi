<?php

namespace AcMarche\Mercredi\Entity\Scolaire;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\ShortNameTrait;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['age_minimum'])]
#[ORM\UniqueConstraint(columns: ['age_maximum'])]
#[ORM\Entity(repositoryClass: GroupeScolaireRepository::class)]
#[UniqueEntity(fields: ['age_minimum'], message: 'Déjà un groupe plaine avec cette âge minimum')]
#[UniqueEntity(fields: ['age_maximum'], message: 'Déjà un groupe plaine avec cette âge maximum')]
class GroupeScolaire implements Stringable
{
    use IdTrait;
    use NomTrait;
    use ShortNameTrait;
    use RemarqueTrait;
    use OrdreTrait;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    #[Assert\LessThan(propertyPath: 'age_maximum')]
    private ?float $age_minimum = null;
    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'age_minimum')]
    private ?float $age_maximum = null;
    /**
     * @var AnneeScolaire[]
     */
    #[ORM\OneToMany(targetEntity: AnneeScolaire::class, mappedBy: 'groupe_scolaire')]
    private Collection $annees_scolaires;

    /**
     * @var array|Enfant[]
     */
    public array $enfants = [];

    public function __construct()
    {
        $this->enfants = [];
        $this->annees_scolaires = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getAgeMinimum(): ?float
    {
        return $this->age_minimum;
    }

    public function setAgeMinimum(?float $age_minimum): self
    {
        $this->age_minimum = $age_minimum;

        return $this;
    }

    public function getAgeMaximum(): ?float
    {
        return $this->age_maximum;
    }

    public function setAgeMaximum(?float $age_maximum): self
    {
        $this->age_maximum = $age_maximum;

        return $this;
    }

    /**
     * @return Collection|AnneeScolaire[]
     */
    public function getAnneesScolaires(): Collection
    {
        return $this->annees_scolaires;
    }

    public function addAnneesScolaire(AnneeScolaire $anneesScolaire): self
    {
        if (!$this->annees_scolaires->contains($anneesScolaire)) {
            $this->annees_scolaires[] = $anneesScolaire;
            $anneesScolaire->setGroupeScolaire($this);
        }

        return $this;
    }

    public function removeAnneesScolaire(AnneeScolaire $anneesScolaire): self
    {
        // set the owning side to null (unless already changed)
        if ($this->annees_scolaires->removeElement($anneesScolaire) && $anneesScolaire->getGroupeScolaire() === $this) {
            $anneesScolaire->setGroupeScolaire(null);
        }

        return $this;
    }
}
