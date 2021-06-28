<?php

namespace AcMarche\Mercredi\Entity;

use Doctrine\Common\Collections\Collection;
use AcMarche\Mercredi\Entity\Traits\ContentTrait;
use AcMarche\Mercredi\Entity\Traits\DocumentsTraits;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Page\Repository\PageRepository")
 */
class Page implements SluggableInterface
{
    public bool $system;
    use IdTrait;
    use NomTrait;
    use ContentTrait;
    use SluggableTrait;
    use DocumentsTraits;

    /**
     * @ORM\Column(type="text", length=100, nullable=true)
     */
    private ?string $slug_system = null;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $position = null;

    /**
     * @var Document[]
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Document")
     */
    private iterable $documents;

    public function __construct()
    {
        $this->system = false;
        $this->documents = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSlugSystem(): ?string
    {
        return $this->slug_system;
    }

    public function setSlugSystem(?string $slug_system): self
    {
        $this->slug_system = $slug_system;

        return $this;
    }
}
