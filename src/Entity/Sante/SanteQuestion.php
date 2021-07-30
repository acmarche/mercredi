<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository")
 */
class SanteQuestion
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private ?string $nom = null;

    /**
     * Information complementaire necessaire.
     * @ORM\Column(type="boolean")
     */
    private bool $complement = false;

    /**
     * Texte d'aide pour le complement.
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $complement_label = null;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private ?int $display_order = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $categorie = null;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteReponse", mappedBy="question", cascade={"remove"})
     */
    private iterable $reponse;

    private ?bool $reponseTxt = null;

    private ?string $remarque = null;

    public function __construct()
    {
        $this->reponse = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function isComplement(): bool
    {
        return $this->complement;
    }

    public function setComplement(bool $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    public function setComplementLabel(?string $complement_label): self
    {
        $this->complement_label = $complement_label;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): self
    {
        $this->display_order = $display_order;

        return $this;
    }

    public function getComplement(): ?bool
    {
        return $this->complement;
    }

    public function getReponseTxt(): ?bool
    {
        return $this->reponseTxt;
    }

    public function setReponseTxt(?bool $reponseTxt): void
    {
        $this->reponseTxt = $reponseTxt;
    }

    /**
     * @return Collection|SanteReponse[]
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(SanteReponse $reponse): self
    {
        if (!$this->reponse->contains($reponse)) {
            $this->reponse[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(SanteReponse $reponse): self
    {
        if ($this->reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
}
