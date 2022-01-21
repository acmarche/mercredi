<?php

namespace AcMarche\Mercredi\Sante\Message;

final class SanteFicheDeleted
{
    public function __construct(
        private int $santeFicheId
    ) {
    }

    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
