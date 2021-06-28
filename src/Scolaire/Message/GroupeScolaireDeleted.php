<?php

namespace AcMarche\Mercredi\Scolaire\Message;

final class GroupeScolaireDeleted
{
    private int $groupeScolaireId;

    public function __construct(int $groupeScolaireId)
    {
        $this->groupeScolaireId = $groupeScolaireId;
    }

    public function getGroupeScolaireId(): int
    {
        return $this->groupeScolaireId;
    }
}
