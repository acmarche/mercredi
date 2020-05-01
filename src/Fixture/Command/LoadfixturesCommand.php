<?php

namespace AcMarche\Mercredi\Fixture\Command;

use AcMarche\Mercredi\Fixture\FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadfixturesCommand extends Command
{
    protected static $defaultName = 'mercredi:loadfixtures';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FixtureLoader
     */
    private $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader,
        EntityManagerInterface $entityManager,
        string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->fixtureLoader = $fixtureLoader;
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

        $questionPurge = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
        $purge = $helper->ask($input, $output, $questionPurge);

        if ($purge) {
            $purger = new ORMPurger($this->entityManager);
            $purger->purge();
        }

        $this->fixtureLoader->load();

        return 0;
    }
}
