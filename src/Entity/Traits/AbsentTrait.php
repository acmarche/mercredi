<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Data\MercrediConstantes;

trait AbsentTrait
{
    /**
     * @var int
     * @see MercrediConstantes::ABSENT_AVEC_CERTIF
     */
    protected $absent = 0;

    /**
     * @return int
     */
    public function getAbsent(): int
    {
        return $this->absent;
    }

    /**
     * @param int $absent
     */
    public function setAbsent(int $absent): void
    {
        $this->absent = $absent;
    }

}
