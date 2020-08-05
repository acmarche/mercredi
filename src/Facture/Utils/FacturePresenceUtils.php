<?php

namespace AcMarche\Mercredi\Facture\Utils;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence;

class FacturePresenceUtils
{
    /**
     * @param FacturePresence[] $facturePresences
     *
     * @return Presence[]
     */
    public static function extractPresence(array $facturePresences): array
    {
        return array_unique(
            array_map(
                function ($facturePresence) {
                    return $facturePresence->getPresence();
                },
                $facturePresences
            ),
            SORT_REGULAR
        );
    }
}
