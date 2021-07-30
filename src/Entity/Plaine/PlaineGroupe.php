<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("plaine_groupe", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"plaine_id", "groupe_scolaire_id"})
 * }))
 * @UniqueEntity({"plaine", "groupe_scolaire"})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineGroupeRepository")
 */
class PlaineGroupe
{
    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=GroupeScolaire::class, inversedBy="plaine_groupes")
     */
    private ?GroupeScolaire $groupe_scolaire=null;

    /**
     * @ORM\ManyToOne(targetEntity=Plaine::class, inversedBy="plaine_groupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Plaine $plaine=null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $inscription_maximum = 0;

    public function __construct(Plaine $plaine, GroupeScolaire $groupe_scolaire)
    {
        $this->plaine = $plaine;
        $this->groupe_scolaire = $groupe_scolaire;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInscriptionMaximum(): ?int
    {
        return $this->inscription_maximum;
    }

    public function setInscriptionMaximum(int $inscription_maximum): self
    {
        $this->inscription_maximum = $inscription_maximum;

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
}
