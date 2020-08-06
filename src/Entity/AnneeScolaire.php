<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class AnneeScolaire
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var Enfant[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Enfant")
     */
    private $enfants;

    /**
     * @var GroupeScolaire
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\GroupeScolaire", inversedBy="annees_scolaires")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $groupeScolaire;

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

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupeScolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupeScolaire): self
    {
        $this->groupeScolaire = $groupeScolaire;

        return $this;
    }
}
