<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\ContentTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Page\Repository\PageRepository")
 */
class Page implements SluggableInterface
{
    use IdTrait;
    use NomTrait;
    use ContentTrait;
    use SluggableTrait;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $system;

    public function __construct()
    {
        $this->system = false;
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return [$this->nom];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }


}
