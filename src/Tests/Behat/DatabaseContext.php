<?php

namespace AcMarche\Mercredi\Tests\Behat;

use Behat\Behat\Context\Context;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseContext implements Context
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var string
     */
    private $pathFixtures;

    public function __construct(ContainerInterface $container)
    {
        $path = $container->getParameter('kernel.project_dir');
        $this->loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');
        $this->pathFixtures = $path.'/src/AcMarche/Mercredi/src/Fixture/Files/';
    }

    /**
     * @BeforeScenario
     */
    public function clearRepositories(): void
    {
        $files =
            [
                $this->pathFixtures.'ecole.yaml',
                $this->pathFixtures.'enfant.yaml',
                $this->pathFixtures.'tuteur.yaml',
                $this->pathFixtures.'user.yaml',
            ];
        $this->loader->load($files);
    }

    /**
     * @AfterScenario
     */
    public function rollbackPostgreSqlTransaction(): void
    {
    }
}
