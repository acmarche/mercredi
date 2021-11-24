<?php

namespace AcMarche\Mercredi\Entity\Presence;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AbsentTrait;
use AcMarche\Mercredi\Entity\Traits\ConfirmedTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\HalfTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JourTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\ReductionTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Presence\Repository\PresenceRepository")
 * @UniqueEntity(fields={"jour", "enfant"}, message="L'enfant est déjà inscrit à cette date")
 */
class Presence implements TimestampableInterface, PresenceInterface, UuidableInterface
{
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use JourTrait;
    use AbsentTrait;
    use OrdreTrait;
    use ReductionTrait;
    use RemarqueTrait;
    use UserAddTrait;
    use TimestampableTrait;
    use HalfTrait;
    use ConfirmedTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Jour::class, inversedBy="presences")
     */
    private ?Jour $jour = null;

    /**
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="presences")
     */
    private ?Enfant $enfant = null;

    /**
     * @ORM\ManyToOne(targetEntity=Tuteur::class, inversedBy="presences")
     */
    private ?Tuteur $tuteur = null;
    /**
     * @var array|Enfant[]
     */
    public array $fratries = [];
    public int $ordreTmp = 0;

    public function __construct(Tuteur $tuteur, Enfant $enfant, Jour $jour)
    {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
        $this->jour = $jour;
        $this->absent = 0;
        $this->half = 0;
    }

    public function __toString()
    {
        return 'presence to string';
    }
}
