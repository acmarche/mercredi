<?php

namespace AcMarche\Mercredi\Fixture\Faker;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use Faker\Provider\Base as BaseProvider;

/**
 * Util pour le chargement des fixtures lors des tests
 * Class CarbonProvider.
 */
final class CarbonProvider extends BaseProvider
{
    public function carbonDateTime(int $year, int $month, int $day, int $hour, int $minute): DateTime
    {
        return Carbon::create($year, $month, $day, $hour, $minute)->toDateTime();
    }

    public function carbonDate(int $year, int $month, int $day): DateTime
    {
        return Carbon::createFromDate($year, $month, $day)->toDateTime();
    }

    /**
     * @param string $format
     * @param string $date
     * @return CarbonImmutable|bool
     */
    public function carbonFromFormat(string $format, string $date)
    {
        return CarbonImmutable::createFromFormat($format, $date);
    }

    public function carbonToday(int $hour, int $minute): Carbon
    {
        $today = Carbon::today();
        $today->setTime($hour, $minute);

        return $today;
    }

    public function carbonAddDays(int $nbDays): Carbon
    {
        $today = Carbon::today();
        $today->addDays($nbDays);

        return $today;
    }
}
