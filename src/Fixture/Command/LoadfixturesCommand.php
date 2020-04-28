<?php

namespace AcMarche\Mercredi\Fixture\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LoadfixturesCommand extends Command
{
    protected static $defaultName = 'mercredi:loadfixtures';
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(LoaderInterface $loader, ParameterBagInterface $parameterBag, string $name = null)
    {
        parent::__construct($name);
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Mercredi/src/Fixture/Files/';
        $files = [
            $path.'ecole.yaml',
            $path.'enfant.yaml',
            $path.'user.yaml',
        ];
        $this->loader->load($files);

        return 0;
    }
}
