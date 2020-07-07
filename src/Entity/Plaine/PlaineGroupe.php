<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\GroupeScolaire;
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
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var GroupeScolaire|null
     *
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\GroupeScolaire")
     */
    private $groupe_scolaire;

    /**
     * @var Plaine|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Plaine\Plaine", inversedBy="plaine_groupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $plaine;

    /**
     * @var int|null
     * @ORM\Column(type="integer")
     */
    private $inscription_maximum;

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

    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
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
