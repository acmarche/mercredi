<?php

namespace AcMarche\Mercredi\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;

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

                return $dateA <=> $dateB;
            }
        );

        return $data;
    }

    /**
     * @param array|Presence[] $presences
     *
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

                return $dateB <=> $dateA;
            }
        );

        return $presences;
    }

    public static function sortGroupesScolairesByOrder(array $groups): array
    {
        uasort(
            $groups,
            function ($dataA, $dataB) {
                $groupeA = $dataA['groupe'];
                $groupeB = $dataB['groupe'];

                return $groupeB->getOrdre() <=> $groupeA->getOrdre();
            }
        );

        return $groups;
    }

    /**
     * @param Enfant[] $data
     *
     * @return Enfant[]
     */
    public static function sortByBirthday(array $data): array
    {
        usort(
            $data,
            function ($enfantA, $enfantB) {
                $dateA = $enfantA->getBirthday();
                $dateA->format('Y-m-d');

                $dateB = $enfantB->getBirthday();
                $dateB->format('Y-m-d');

                if ($dateA === $dateB) {
                    if ($enfantA->getPrenom() > $enfantB->getPrenom()) {
                        return +1;
                    }

                    return -1;
                }

                return $dateA > $dateB ? +1 : -1;
            }
        );

        return $data;
    }

    /**
     * @param Enfant[]|Tuteur[] $data
     *
     * @return Enfant[]|Tuteur[]|array
     */
    public static function sortByName(array $data): array
    {
        usort(
            $data,
            function ($enfantA, $enfantB) {
                $nameA = $enfantA->getNom();
                $nameB = $enfantB->getNom();

                if ($nameA === $nameB) {
                    if ($enfantA->getPrenom() > $enfantB->getPrenom()) {
                        return +1;
                    }

                    return -1;
                }

                return $nameA > $nameB ? +1 : -1;
            }
        );

        return $data;
    }
}
