<?php

namespace AcMarche\Mercredi\Presence\Message;

final class PresenceDeleted
{
    private int $presenceId;

    public function __construct(int $presenceId)
    {
        $this->presenceId = $presenceId;
    }

    public function getPresenceId(): int
    {
        return $this->presenceId;
    }
}
