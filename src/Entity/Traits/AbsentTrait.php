<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Data\MercrediConstantes;

trait AbsentTrait
{
    /**
     * @var int
     *
     * @see MercrediConstantes::ABSENCE_AVEC_CERTIF
     */
    private $absent;

    public function getAbsent(): int
    {
        return $this->absent;
    }

    public function setAbsent(int $absent): void
    {
        $this->absent = $absent;
    }
}
