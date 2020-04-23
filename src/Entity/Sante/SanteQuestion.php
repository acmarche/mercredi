<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity()
 */
class SanteQuestion
{
    use IdTrait,
        NomTrait;

    /**
     * Information complementaire necessaire.
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $complement = false;

    /**
     * Texte d'aide pour le complement.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $complement_label;

    /**
     * @var int|null
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $display_order;

    /**
     * @var int|null
     * 0 => Non, 1 => Oui, -1 => Pas de reponse
     */
    protected $reponse;

    /**
     * @var string|null
     */
    protected $remarque;

    /**
     * @var SanteFiche|null
     */
    protected $sante_fiche;

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return bool
     */
    public function isComplement(): bool
    {
        return $this->complement;
    }

    /**
     * @param bool $complement
     */
    public function setComplement(bool $complement): void
    {
        $this->complement = $complement;
    }

    /**
     * @return string|null
     */
    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    /**
     * @param string|null $complement_label
     */
    public function setComplementLabel(?string $complement_label): void
    {
        $this->complement_label = $complement_label;
    }

    /**
     * @return int|null
     */
    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    /**
     * @param int|null $display_order
     */
    public function setDisplayOrder(?int $display_order): void
    {
        $this->display_order = $display_order;
    }

    /**
     * @return int|null
     */
    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    /**
     * @param int|null $reponse
     */
    public function setReponse(?int $reponse): void
    {
        $this->reponse = $reponse;
    }

    /**
     * @return string|null
     */
    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    /**
     * @param string|null $remarque
     */
    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

    /**
     * @return SanteFiche|null
     */
    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    /**
     * @param SanteFiche|null $sante_fiche
     */
    public function setSanteFiche(?SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

}
