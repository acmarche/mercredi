<?php

namespace AcMarche\Mercredi\Tests\Communication;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactoryHotton;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommunicationFactoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        //$routerService = self::$container->get('router');
        //$myCustomService = self::$container->get(CustomService::class);
        $container = static::getContainer();
        $communicationFactory = $container->get(CommunicationFactoryHotton::class);
        $facture = new Facture(new Tuteur());
        $facture->setMois('09-2021');
        $year = 2021;
        $short = substr($year, -2, 2);
        self::assertSame('21', $short);
        $communication = $communicationFactory->generate($facture);

        self::assertSame('ez', $communication);
    }
}
