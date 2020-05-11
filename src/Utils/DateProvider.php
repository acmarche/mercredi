<?php


namespace AcMarche\Mercredi\Utils;


class DateProvider
{
    /**
     * @param string $date "01/08/2018"
     *
     * @return \DatePeriod
     *
     * @throws \Exception
     */
    public static function getDateIntervale(string $date): \DatePeriod
    {
        $begin = \DateTimeImmutable::createFromFormat('d/m/Y', $date);
        $end = $begin->modify('last day of this month');
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');

        return new \DatePeriod($begin, $interval, $end);
    }

}
