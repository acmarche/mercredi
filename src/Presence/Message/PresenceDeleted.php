<?php

namespace AcMarche\Mercredi\Presence\Message;

final class PresenceDeleted
{
    public function __construct(
        private int $presenceId
    ) {
    }

    public function getPresenceId(): int
    {
        return $this->presenceId;
    }
}
