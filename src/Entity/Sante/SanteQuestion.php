<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository")
 */
class SanteQuestion
{
    use IdTrait;
    use NomTrait;

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
     *               0 => Non, 1 => Oui, -1 => Pas de reponse
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

    public function isComplement(): bool
    {
        return $this->complement;
    }

    public function setComplement(bool $complement): void
    {
        $this->complement = $complement;
    }

    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    public function setComplementLabel(?string $complement_label): void
    {
        $this->complement_label = $complement_label;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): void
    {
        $this->display_order = $display_order;
    }

    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    public function setReponse(?int $reponse): void
    {
        $this->reponse = $reponse;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }

    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(?SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }

    public function getComplement(): ?bool
    {
        return $this->complement;
    }
}
