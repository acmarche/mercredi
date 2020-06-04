<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait AgeTrait
{
    /**
     * @var \DateTime
     */
    private $birthday;

    public function getAge(\DateTime $date_reference = null, $month = false): string
    {
        $birthday = $this->birthday;

        if (!$birthday) {
            return '';
        }

        $today = new \DateTime();

        if ($date_reference) {
            $today = $date_reference;
        }

        $date = $birthday->diff($today);

        if ($month) {
            return $date->format('%y ans et %m mois');
        }

        return $date->format('%y');
    }
}
