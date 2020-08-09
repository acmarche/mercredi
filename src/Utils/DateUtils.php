<?php

namespace AcMarche\Mercredi\Utils;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use Locale;

final class DateUtils
{
    /**
     * @param string $mois 05/2020
     *
     * @return DateTime
     * @throws Exception
     */
    public static function createDateTimeFromDayMonth(string $mois): DateTime
    {
        if ($date = DateTime::createFromFormat('d/m/Y', '01/'.$mois)) {
            return $date;
        }

        throw new Exception('Mauvais format de date: '.$mois);
    }

    public static function formatFr(DateTime $dateTime, string $format = IntlDateFormatter::FULL): string
    {
        $intlDateFormatter = new IntlDateFormatter(
            Locale::getDefault(),
            $format,
            IntlDateFormatter::NONE,
            new DateTimeZone('Europe/Brussels'),
            IntlDateFormatter::GREGORIAN
        );

        return $intlDateFormatter->format($dateTime);
    }

    /**
     * @param DateTime $dateTime "01/08/2018"
     *
     * @return DatePeriod
     */
    public static function getDatePeriod(DateTime $dateTime): DatePeriod
    {
        $begin = DateTimeImmutable::createFromMutable($dateTime);
        $end = $begin->modify('last day of this month');
        $end = $end->modify('+1 day');

        $dateInterval = new DateInterval('P1D');

        return new DatePeriod($begin, $dateInterval, $end);
    }
}
