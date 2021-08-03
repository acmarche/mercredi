<?php

namespace AcMarche\Mercredi\Utils;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;

final class SortUtils
{
    /**
     * @param Jour[] $data
     *
     * @return Jour[]
     */
    public static function sortJoursByDateTime(array $data): array
    {
        usort(
            $data,
            function ($jourA, $jourB) {
                $dateA = $jourA->getDateJour();
                $dateA->format('Y-m-d');

                $dateB = $jourB->getDateJour();
                $dateB->format('Y-m-d');

                if ($dateA === $dateB) {
                    return 0;
                }

                return $dateA > $dateB ? -1 : 1;
            }
        );

        return $data;
    }

    /**
     * @param array|Presence[] $presences
     * @return Presence[]
     */
    public static function sortPresences(array $presences): array
    {
        usort(
            $presences,
            function ($presenceA, $presenceB) {
                $dateA = $presenceA->getJour()->getDateJour();
                $dateA->format('Y-m-d');

                $dateB = $presenceB->getJour()->getDateJour();
                $dateB->format('Y-m-d');

                if ($dateA === $dateB) {
                    return 0;
                }

                return $dateA > $dateB ? 1 : -1;
            }
        );

        return $presences;
    }

    public static function sortGroupesScolairesByOrder(array $groups)
    {
        uasort(
            $groups,
            function ($dataA, $dataB) {

                $groupeA = $dataA['groupe'];
                $groupeB = $dataB['groupe'];

                if ($groupeA->getOrdre() === $groupeB->getOrdre()) {
                    return 0;
                }

                return $groupeA->getOrdre() > $groupeB->getOrdre() ? 1 : -1;
            }
        );

        return $groups;
    }
}
