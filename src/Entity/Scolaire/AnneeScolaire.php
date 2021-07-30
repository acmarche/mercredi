<?php

namespace AcMarche\Mercredi\Entity\Scolaire;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository")
 */
class AnneeScolaire
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @ORM\OneToOne(targetEntity=AnneeScolaire::class)
     * @ORM\JoinColumn(onDelete="SET NULL", unique=true)
     */
    private ?AnneeScolaire $annee_suivante = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $ordre = null;

    /**
     * @var Enfant[]
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="annee_scolaire")
     */
    private iterable $enfants;

    /**
     * @ORM\ManyToOne(targetEntity=GroupeScolaire::class, inversedBy="annees_scolaires")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?GroupeScolaire $groupe_scolaire = null;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
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
            $enfant->setAnneeScolaire($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getAnneeScolaire() === $this) {
                $enfant->setAnneeScolaire(null);
            }
        }

        return $this;
    }

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getAnneeSuivante(): ?self
    {
        return $this->annee_suivante;
    }

    public function setAnneeSuivante(?self $annee_suivante): self
    {
        $this->annee_suivante = $annee_suivante;

        return $this;
    }
}
