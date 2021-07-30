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

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Ecole\Repository\EcoleRepository")
 */
class Ecole
{
    use IdTrait;
    use NomTrait;
    use AdresseTrait;
    use TelephonieTrait;
    use EmailTrait;
    use RemarqueTrait;
    use UsersTrait;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="ecoles" )
     *
     * @var User[]|Collection
     */
    private iterable $users;
    /**
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="ecole" )
     *
     * @var Enfant[]|Collection
     */
    private iterable $enfants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
            $enfant->setEcole($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getEcole() === $this) {
                $enfant->setEcole(null);
            }
        }

        return $this;
    }
}
