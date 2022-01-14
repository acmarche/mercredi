<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Data\MercrediConstantes;
use Doctrine\ORM\Mapping as ORM;

trait AbsentTrait
{
    /**
     * @see MercrediConstantes::ABSENCE_AVEC_CERTIF
     */
    #[ORM\Column(type: 'smallint', length: 2, nullable: false, options: ['comment' => '-1 sans certif, 1 avec certfi'])]
    private int $absent = 0;

    public function getAbsent(): int
    {
        return $this->absent;
    }

    public function setAbsent(int $absent): void
    {
        $this->absent = $absent;
    }
}
