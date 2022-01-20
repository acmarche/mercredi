<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UsersTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JoursTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Animateur\Repository\AnimateurRepository')]
class Animateur implements TimestampableInterface
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use AdresseTrait;
    use EmailTrait;
    use RemarqueTrait;
    use SexeTrait;
    use TelephonieTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use UserAddTrait;
    use UsersTrait;
    use JoursTrait;
    /**
     * @var User[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'animateurs')]
    private Collection $users;
    /**
     * @var Jour[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: Jour::class, inversedBy: 'animateurs')]
    private Collection $jours;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->jours = new ArrayCollection();
    }

    public function __toString(): string
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }
}
