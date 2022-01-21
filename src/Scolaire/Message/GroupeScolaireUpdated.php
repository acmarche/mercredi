<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class GroupeScolaireUpdated
{
    public function __construct(
        private int $groupeScolaireId
    ) {
    }

    public function getGroupeScolaireId(): int
    {
        return $this->groupeScolaireId;
    }
}
