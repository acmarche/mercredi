<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("plaine_max", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"plaine_id", "groupe"})
 * }))
 * @UniqueEntity({"plaine", "groupe"})
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineMaxRepository")
 */
class PlaineMax
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
     * @var string|null
     *
     * @ORM\Column(type="string", length=50)
     */
    private $groupe;

    /**
     * @var Plaine|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Plaine\Plaine", inversedBy="max", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $plaine;

    /**
     * @var int|null
     * @ORM\Column(type="integer")
     */
    private $maximum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(?string $groupe): void
    {
        $this->groupe = $groupe;
    }

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }

    public function getMaximum(): ?int
    {
        return $this->maximum;
    }

    public function setMaximum(?int $maximum): void
    {
        $this->maximum = $maximum;
    }
}
