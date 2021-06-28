<?php

namespace AcMarche\Mercredi\Sante\Message;

final class SanteFicheCreated
{
    private int $santeFicheId;

    public function __construct(int $santeFicheId)
    {
        $this->santeFicheId = $santeFicheId;
    }

    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
