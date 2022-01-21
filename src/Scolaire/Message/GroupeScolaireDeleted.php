<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class GroupeScolaireDeleted
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
