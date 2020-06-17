<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait TuteursTrait
{
    /**
     * @var Tuteur[]
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="users")
     */
    private $tuteurs = []; //va pas dans construct

    /**
     * @return Collection|Tuteur[]
     */
    public function getTuteurs(): iterable
    {
        return $this->tuteurs;
    }
}
