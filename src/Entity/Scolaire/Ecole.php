<?php

namespace AcMarche\Mercredi\Entity\Scolaire;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\Traits\UsersTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Ecole\Repository\EcoleRepository')]
class Ecole
{
    use IdTrait;
    use NomTrait;
    use AdresseTrait;
    use TelephonieTrait;
    use EmailTrait;
    use RemarqueTrait;
    use UsersTrait;
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $abreviation = null;
    /**
     * @var User[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'ecoles')]
    private iterable $users;
    /**
     * @var Enfant[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Enfant::class, mappedBy: 'ecole')]
    private iterable $enfants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    public function __toString(): string
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
            $enfant->setEcole($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        // set the owning side to null (unless already changed)
        if ($this->enfants->removeElement($enfant) && $enfant->getEcole() === $this) {
            $enfant->setEcole(null);
        }

        return $this;
    }

    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(?string $abreviation): self
    {
        $this->abreviation = $abreviation;

        return $this;
    }
}
