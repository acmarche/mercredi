<?php

namespace AcMarche\Mercredi\Tests\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Calculator\PrenceHottonCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorHottonTest extends KernelTestCase
{
    public function testSettingCustomerFirstName(): void
    {
        $container = static::getContainer();
        $calculator = $container->get(PrenceHottonCalculator::class);

        $presence = new Presence();
        $calculator->calculate($presence);
    }

    public function testAbsent(): void
    {
        $container = static::getContainer();
        $calculator = $container->get(PrenceHottonCalculator::class);
        $tuteur = new Tuteur();
        $enfant = new Enfant();
        $jour = new Jour();

        $presence = new Presence($tuteur, $enfant, $jour);
        $presence->setAbsent(MercrediConstantes::ABSENCE_AVEC_CERTIF);
        self::assertSame(0, $calculator->calculate($presence));
    }
}
