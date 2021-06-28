<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait BirthdayTrait
{
    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private ?DateTimeInterface $birthday;

    public function getBirthday(): ?DateTime
    {
        return $this->birthday;
    }

    /**
     * @param DateTime|DateTimeImmutable|null $birthday
     */
    public function setBirthday(?DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param DateTime|DateTimeImmutable|null $date_reference
     */
    public function getAge(?DateTimeInterface $date_reference = null, $month = false): string
    {
        $birthday = $this->birthday;

        if (!$birthday) {
            return '';
        }

        $today = new DateTime();

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
