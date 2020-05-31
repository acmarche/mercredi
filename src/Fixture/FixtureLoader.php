<?php


namespace AcMarche\Mercredi\Fixture;

use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FixtureLoader
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        LoaderInterface $loader,
        ParameterBagInterface $parameterBag
    ) {
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
    }

    public function getPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Mercredi/src/Fixture/Files/';
    }

    public function load(): void
    {
        $path = $this->getPath();

        $files = [
            $path.'ecole.yaml',
            $path.'tuteur.yaml',
            $path.'enfant.yaml',
            $path.'relation.yaml',
            $path.'user.yaml',
            $path.'jour.yaml',
            $path.'organisation.yaml',
            $path.'presence.yaml',
            $path.'reduction.yaml',
            $path.'question.yaml',
        ];

        $this->loader->load($files);
    }
}
