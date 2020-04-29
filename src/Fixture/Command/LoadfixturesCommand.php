<?php

namespace AcMarche\Mercredi\Fixture\Command;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
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
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        LoaderInterface $loader,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Chargment des fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $path = $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Mercredi/src/Fixture/Files/';

        $questionPurge = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
        $purge = $helper->ask($input, $output, $questionPurge);

        if ($purge) {
            $purger = new ORMPurger($this->entityManager);
            $purger->purge();
        }

        $files = [
            $path.'ecole.yaml',
            $path.'tuteur.yaml',
            $path.'enfant.yaml',
            $path.'user.yaml',
            $path.'jour.yaml',
            $path.'organisation.yaml',
            $path.'reduction.yaml',
        ];

        $this->loader->load($files);

        return 0;
    }
}
