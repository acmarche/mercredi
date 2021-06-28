<?php

namespace AcMarche\Mercredi\Twig;

use AcMarche\Mercredi\Data\MercrediConstantes;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class MercrediExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    private const MONTHS = [
        1 => 'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',
    ];

    public function getFilters(): array
    {
        return [
            new TwigFilter('mercredi_month_fr', fn(int $number) => $this->monthFr($number)),
            new TwigFilter('mercredi_absence_text', fn($number): string => $this->absenceFilter($number)),
        ];
    }

    public function absenceFilter($number): string
    {
        return MercrediConstantes::getAbsenceTxt($number);
    }

    public function monthFr(int $number)
    {
        return isset(self::MONTHS[$number]) ? self::MONTHS[$number] : $number;
    }
}
