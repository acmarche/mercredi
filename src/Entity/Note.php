<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Enfant;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Note\Repository\NoteRepository")
 */
class Note implements TimestampableInterface
{
    use IdTrait;
    use RemarqueTrait;
    use TimestampableTrait;
    use ArchiveTrait;
    use UserAddTrait;
    use EnfantTrait;

    /**
     * @ORM\ManyToOne (targetEntity=Enfant::class, inversedBy="notes")
     */
    private $enfant;

    public function __toString()
    {
        return $this->getRemarque();
    }


}
