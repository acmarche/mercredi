<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("relation", uniqueConstraints={
 * @ORM\UniqueConstraint(columns={"tuteur_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Relation\Repository\RelationRepository")
 * @UniqueEntity(fields={"tuteur", "enfant"}, message="Cet enfant est déjà lié à ce parent")
 */
class Relation
{
    use IdTrait;
    use TuteurTrait;
    use EnfantTrait;
    use OrdreTrait;

    /**
     * @ORM\Column(type="string", length=200, nullable=true, options={"comment" = "pere,mere,beau pere.."})
     */
    private ?string $type = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant", inversedBy="relations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * */
    private ?Enfant $enfant = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="relations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     * */
    private ?Tuteur $tuteur=null;

    public function __construct(Tuteur $tuteur, Enfant $enfant)
    {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
