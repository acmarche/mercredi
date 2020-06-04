<?php

namespace AcMarche\Mercredi\Sante\Message;

class SanteFicheUpdated
{
    /**
     * @var int
     */
    private $santeFicheId;

    public function __construct(int $santeFicheId)
    {
        $this->santeFicheId = $santeFicheId;
    }

    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
