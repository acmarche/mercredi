<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AbsentTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JourTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\ReductionTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Traits\UserAddTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table("presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Presence\Repository\PresenceRepository")
 * @UniqueEntity(fields={"jour", "enfant"}, message="L'enfant est déjà inscrit à cette date")
 */
class Presence implements TimestampableInterface
{
    use IdTrait;
    use EnfantTrait;
    use TuteurTrait;
    use JourTrait;
    use AbsentTrait;
    use OrdreTrait;
    use ReductionTrait;
    use RemarqueTrait;
    use UserAddTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Jour", inversedBy="presences")
     */
    protected $jour;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant", inversedBy="presences")
     */
    protected $enfant;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="presences")
     */
    protected $tuteur;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Reduction")
     */
    protected $reduction;

    /**
     * @ORM\Column(type="smallint", length=2, nullable=false, options={"comment" = "-1 sans certif, 1 avec certfi"})
     */
    protected $absent = 0;

    public function __construct(Tuteur $tuteur, Enfant $enfant, Jour $jour)
    {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
        $this->jour = $jour;
    }

    public function __toString()
    {
        $date_jour = $this->jour->getDateJour();

        return 'presence to string';
    }
}
