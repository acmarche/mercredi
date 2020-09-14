<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Carbon\Carbon;

trait AgeTrait
{
    /**
     * @var \DateTime
     */
    private $birthday;

    public function getAge(?\DateTime $date_reference = null, $month = false): string
    {
        $birthday = $this->birthday;

        if (!$birthday) {
            return '';
        }

        $today = new \DateTime();

        if (null !== $date_reference) {
            $today = $date_reference;
        }

        $date = $birthday->diff($today);

        if ($month) {
            return $date->format('%y ans et %m mois');
        }

        return $date->format('%y');
    }

    /**
     * alternative
     */
    public function getAge2(): int
    {
        $daysSinceEpoch = Carbon::createFromDate(1975, 5, 21)->diffInDays();
        $howOldAmI = Carbon::createFromDate(1975, 5, 21)->age;
    }
}
