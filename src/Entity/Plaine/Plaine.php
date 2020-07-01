<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineRepository")
 */
class Plaine
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    use PlaineJoursTrait;
    use JoursTrait;
    use InscriptionOpenTrait;
    use PrixTrait;
    use PrematernelleTrait;
    use PlaineMaxTrait;

    /**
     * @var Jour[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Plaine\PlaineJour", mappedBy="plaine", cascade={"persist","remove"})
     */
    private $plaine_jours;

    /**
     * @var PlaineMax[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Plaine\PlaineMax", mappedBy="plaine", cascade={"remove","persist"})
     */
    private $max;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->max = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
        $this->plaine_jours = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection|PlaineMax[]
     */
    public function getMax(): Collection
    {
        return $this->max;
    }

    public function addMax(PlaineMax $max): self
    {
        if (!$this->max->contains($max)) {
            $this->max[] = $max;
            $max->setPlaine($this);
        }

        return $this;
    }

    public function removeMax(PlaineMax $max): self
    {
        if ($this->max->contains($max)) {
            $this->max->removeElement($max);
            // set the owning side to null (unless already changed)
            if ($max->getPlaine() === $this) {
                $max->setPlaine(null);
            }
        }

        return $this;
    }


}
