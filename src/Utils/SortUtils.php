<?php

namespace AcMarche\Mercredi\Utils;

use AcMarche\Mercredi\Entity\Jour;

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
}
