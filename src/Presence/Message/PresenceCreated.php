<?php

namespace AcMarche\Mercredi\Presence\Message;

class PresenceCreated
{
    /**
     * @var array
     */
    private $days;

    public function __construct(array $days)
    {
        $this->days = $days;
    }

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

}
