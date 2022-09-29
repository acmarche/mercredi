<?php

namespace AcMarche\Mercredi\Tests\Plaine;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Plaine\Handler\PlaineHandlerMarche;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InscriptionTest extends KernelTestCase
{
    public function testSomething()
    {
        self::bootKernel();
        $container = static::getContainer();
        $plaineRepository = $container->get(PlaineRepository::class);
        $tuteurRepository = $container->get(TuteurRepository::class);
        $enfantRepository = $container->get(EnfantRepository::class);
        $plaineHandler = $container->get(PlaineHandlerMarche::class);

        $plaine = $plaineRepository->findOneBy(['slug' => 'plaine-d-automne-2022']);
        $tuteur = $tuteurRepository->findOneBy(['slug' => 'dubuisson-nathalie']);
        $enfant = $enfantRepository->findOneBy(['slug' => 'brolet-louise']);

        self::assertSame('Brolet', $enfant->getNom());
        $jours = $plaine->getJours();

        $daysFull = $plaineHandler->handleAdddubuEnfant($plaine, $tuteur, $enfant, $jours);
        self::assertCount(5, $daysFull);

        $days = [
            '24-10',
            '25-10',
            '26-10',
            '27-10',
            '28-10',
        ];

        foreach ($daysFull as $day) {
            self::assertContains($day->format('d-m'), $days);
        }

    }
}