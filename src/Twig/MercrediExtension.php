<?php

namespace AcMarche\Mercredi\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MercrediExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('mercredi_month_fr', [$this, 'monthFr']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function monthFr(int $number)
    {
        $months = [
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

        return isset($months[$number]) ? $months[$number] : $number;
    }

}
