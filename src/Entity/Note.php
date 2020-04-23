<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\UserAddTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Note\Repository\NoteRepository")
 */
class Note implements TimestampableInterface
{
    use IdTrait,
        RemarqueTrait,
        TimestampableTrait,
        ArchiveTrait,
        UserAddTrait;

    public function __toString()
    {
        return 'Note '.(string)$this->id;
    }
}
