<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Facture\Validator as AcMarcheValidator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FactureComplementRepository")
 * @ORM\Table("facture_complement")
 * @AcMarcheValidator\PourcentageOrForfait()
 */
class FactureComplement
{
    use IdTrait;
    use NomTrait;
    use FactureTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Facture::class, inversedBy="factureComplements")
     */
    private Facture $facture;
    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(
     *      min = 0
     * )
     */
    private ?float $forfait = null;
    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     *)
     */
    private ?float $pourcentage = null;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }
}
