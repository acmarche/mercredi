<?php

namespace AcMarche\Mercredi\Utils;

class DateUtils
{
    /**
     * @param string $mois 05/2020
     *
     * @throws \Exception
     */
    public static function createDateTimeFromDayMonth(string $mois): \DateTime
    {
        if ($date = \DateTime::createFromFormat('d/m/Y', '01/'.$mois)) {
            return $date;
        }
        throw new \Exception('Mauvais format de date: '.$mois);
    }

    public static function formatFr(\DateTime $dateTime, $format = \IntlDateFormatter::FULL): string
    {
        $formatter = new \IntlDateFormatter(
            \Locale::getDefault(),
            $format,
            \IntlDateFormatter::NONE,
            new \DateTimeZone('Europe/Brussels'),
            \IntlDateFormatter::GREGORIAN
        );

        return $formatter->format($dateTime);
    }

    /**
     * @param string $date "01/08/2018"
     *
     * @throws \Exception
     */
    public static function getDatePeriod(\DateTime $date): \DatePeriod
    {
        $begin = \DateTimeImmutable::createFromMutable($date);
        $end = $begin->modify('last day of this month');
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');

        return new \DatePeriod($begin, $interval, $end);
    }
}
