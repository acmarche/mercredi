<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Note\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note implements TimestampableInterface, Stringable
{
    use IdTrait;
    use RemarqueTrait;
    use TimestampableTrait;
    use ArchiveTrait;
    use UserAddTrait;
    use EnfantTrait;

    #[ORM\ManyToOne(targetEntity: Enfant::class, inversedBy: 'notes')]
    private ?Enfant $enfant = null;

    public function __construct(
        ?Enfant $enfant
    ) {
        $this->enfant = $enfant;
    }

    public function __toString(): string
    {
        return $this->getRemarque();
    }
}
