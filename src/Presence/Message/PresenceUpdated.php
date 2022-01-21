<?php

namespace AcMarche\Mercredi\Presence\Message;

final class PresenceUpdated
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
