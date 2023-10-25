<?php

namespace AcMarche\Mercredi\Tests\Calculator;

use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Entity\Tuteur;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorMarcheTest extends KernelTestCase
{
    public function testAbsent(): void
    {
        $container = static::getContainer();
        $calculator = $container->get(PresenceCalculatorInterface::class);
        $tuteur = new Tuteur();
        $enfant = new Enfant();
        $jour = new Jour();

        $presence = new Presence($tuteur, $enfant, $jour);
        $presence->setAbsent(MercrediConstantes::ABSENCE_AVEC_CERTIF);
        self::assertSame(0.0, $calculator->calculate($presence));
    }

    public function testOrdre(): void
    {
        $container = static::getContainer();
        $calculator = $container->get(PresenceCalculatorInterface::class);

        $jour = new  Jour(new DateTime());
        $jour->setPrix1(3);
        $jour->setPrix2(2);
        $jour->setPrix3(1);

        $presence = new Presence(new Tuteur(), new Enfant(), $jour);

        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 4));
    }

    public function testPedagogique()
    {
        $calculator = $this->getCalculator();
        $jour = new  Jour(new DateTime());
        $jour->setPedagogique(true);
        $jour->setPrix1(3);
        $jour->setPrix2(2);
        $jour->setPrix3(1);

        $presence = new Presence(new Tuteur(), new Enfant(), $jour);
        $presence->setHalf(true);
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 4));
        $presence->setHalf(false);
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 0));
        self::assertSame(3.0, $calculator->getPrixByOrdre($presence, 1));
        self::assertSame(2.0, $calculator->getPrixByOrdre($presence, 2));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 3));
        self::assertSame(1.0, $calculator->getPrixByOrdre($presence, 4));
    }

    public function testReduction(): void
    {
        $container = static::getContainer();
        $calculator = $container->get(PresenceCalculatorInterface::class);
        $tuteur = new Tuteur();
        $tuteur->id = 1;
        $enfant = new Enfant();
        $enfant->id = 1;
        $jour = new Jour();
        $jour->setPrix1(7);
        $reduction = new Reduction();
        $reduction->amount = 4;
        $reduction->is_forfait = true;

        $presence = new Presence($tuteur, $enfant, $jour);
        $presence->setReduction($reduction);
        self::assertSame(4.0, $calculator->calculate($presence));

        $reduction->is_forfait = false;
        $presence->setReduction($reduction);
        self::assertSame(3.0, $calculator->calculate($presence));

        $reduction->amount = null;
        $reduction->pourcentage = 10;
        $presence->setReduction($reduction);
        self::assertSame(6.3, $calculator->calculate($presence));
    }

    public function testPlaine()
    {
        $calculator = $this->getCalculator();
        $plaine = new Plaine();
        $plaine->setPrix1(3);
        $plaine->setPrix2(2);
        $plaine->setPrix3(1);

        $jour = new Jour(new DateTime());
        $jour->setPrix1(3);
        $jour->setPrix2(2);
        $jour->setPrix3(1);
        $jour->setPlaine($plaine);

        $tuteur = new Tuteur();
        $tuteur->id = 1;

        $enfant = new Enfant();
        $enfant->id = 1;

        $presence = new Presence($tuteur, $enfant, $jour);
        self::assertSame(3.0, $calculator->calculate($presence));
        self::assertSame(3.0, $calculator->calculate($presence));
        self::assertSame(2.0, $calculator->calculate($presence));
        self::assertSame(1.0, $calculator->calculate($presence));
        self::assertSame(1.0, $calculator->calculate($presence));
    }

    private function getCalculator(): PresenceCalculatorInterface
    {
        $container = static::getContainer();

        return $container->get(PresenceCalculatorInterface::class);
    }
}
