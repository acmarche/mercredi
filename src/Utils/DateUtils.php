<?php


namespace AcMarche\Mercredi\Utils;


class DateUtils
{
    /**
     * @param string $mois 05/2020
     * @return \DateTime
     * @throws \Exception
     */
    public static function createDateTimeFromDayMonth(string $mois): \DateTime
    {
        if ($date = \DateTime::createFromFormat('d/m/Y', '01/'.$mois)) {
            return $date;
        }
        throw new \Exception('Impossible de créer un dateTime depuis le string '.$mois);
    }

}
