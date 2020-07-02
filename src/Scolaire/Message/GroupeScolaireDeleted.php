<?php

namespace AcMarche\Mercredi\Scolaire\Message;

class GroupeScolaireDeleted
{
    /**
     * @var int
     */
    private $groupeScolaireId;

    public function __construct(int $groupeScolaireId)
    {
        $this->groupeScolaireId = $groupeScolaireId;
    }

    public function getGroupeScolaireId(): int
    {
        return $this->groupeScolaireId;
    }
}
