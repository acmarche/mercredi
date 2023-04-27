<?php
namespace AcMarche\Mercredi\Calendar;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;

class CarbonFactory
{
    public function today(): CarbonInterface
    {
        return $this->setLocale(Carbon::today());
    }

    public function instance(DateTimeInterface $date): CarbonInterface
    {
        return $this->setLocale(Carbon::instance($date));
    }

    public function instanceImmutable(DateTimeInterface $date): CarbonImmutable
    {
        $dateCreated = $this->setLocale(Carbon::instance($date));

        return $dateCreated->toImmutable();
    }

    public function setLocale(CarbonInterface $date): CarbonInterface|string
    {
        return $date->locale('fr');
    }
}
