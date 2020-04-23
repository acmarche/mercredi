<?php

namespace AcMarche\Mercredi\Sante\Message;

class SanteFicheDeleted
{
    /**
     * @var int
     */
    private $santeFicheId;

    public function __construct(int $santeFicheId)
    {
        $this->santeFicheId = $santeFicheId;
    }

    /**
     * @return int
     */
    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
