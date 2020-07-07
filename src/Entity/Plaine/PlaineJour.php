<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table("plaine_jour", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "plaine_id"})
 * }))
 * @UniqueEntity({"jour", "plaine"})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineJourRepository")
 */
class PlaineJour
{
    use IdTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Plaine\Plaine", inversedBy="plaine_jours")
     */
    private $plaine;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Jour")
     */
    private $jour;

    public function __construct(Plaine $plaine, Jour $jour)
    {
        $this->plaine = $plaine;
        $this->jour = $jour;
    }

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): self
    {
        $this->plaine = $plaine;

        return $this;
    }

    public function getJour(): ?Jour
    {
        return $this->jour;
    }

    public function setJour(?Jour $jour): self
    {
        $this->jour = $jour;

        return $this;
    }
}
