<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Presence;

trait PresenceTrait
{
    /**
     * @var Presence
     */
    private $presence;

    public function getPresence(): Presence
    {
        return $this->presence;
    }

    public function setPresence(Presence $presence): void
    {
        $this->presence = $presence;
    }
}
