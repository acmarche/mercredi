<?php

namespace AcMarche\Mercredi\Facture\Utils;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence;

final class FacturePresenceUtils
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
                fn($facturePresence) => $facturePresence->getPresence(),
                $facturePresences
            ),
            SORT_REGULAR
        );
    }
}
