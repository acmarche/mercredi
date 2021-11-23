<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\CreanceRepository")
 *
 */
class Creance implements TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use NomTrait;
    use TuteurTrait;
    use TimestampableTrait;
    use RemarqueTrait;
    use UuidableTrait;
    use UserAddTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Tuteur::class, inversedBy="creances")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Tuteur $tuteur = null;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     * @Assert\Range(
     *      min = 0.1
     * )
     */
    private ?float $montant = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $dateLe = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $payeLe = null;

    public function __construct(Tuteur $tuteur)
    {
        $this->tuteur = $tuteur;
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateLe(): ?\DateTimeInterface
    {
        return $this->dateLe;
    }

    public function setDateLe(?\DateTimeInterface $dateLe): self
    {
        $this->dateLe = $dateLe;

        return $this;
    }

    public function getPayeLe(): ?\DateTimeInterface
    {
        return $this->payeLe;
    }

    public function setPayeLe(?\DateTimeInterface $payeLe): self
    {
        $this->payeLe = $payeLe;

        return $this;
    }
}


