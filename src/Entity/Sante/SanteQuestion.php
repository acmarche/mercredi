<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity()
 */
class SanteQuestion
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    /**
     * Information complementaire necessaire.
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private const COMPLEMENT = false;

    /**
     * Texte d'aide pour le complement.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complement_label;

    /**
     * @var int|null
     * @ORM\Column(type="integer",nullable=true)
     */
    private $display_order;

    private $reponseTxt;

    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): void
    {
        $this->display_order = $display_order;
    }

    public function getComplement(): ?bool
    {
        return self::COMPLEMENT;
    }

    /**
     * @return mixed
     */
    public function getReponseTxt()
    {
        return $this->reponseTxt;
    }
}
