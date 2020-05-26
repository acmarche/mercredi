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

}
