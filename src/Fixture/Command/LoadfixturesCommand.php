<?php

namespace AcMarche\Mercredi\Fixture\Command;

use AcMarche\Mercredi\Fixture\FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

final class LoadfixturesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'mercredi:load-fixtures';
    private EntityManagerInterface $entityManager;
    private FixtureLoader $fixtureLoader;

    public function __construct(
        FixtureLoader $fixtureLoader,
        EntityManagerInterface $entityManager,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->fixtureLoader = $fixtureLoader;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Chargment des fixtures')
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $purge = $input->getOption('purge');

        if ($purge === null) {
            $confirmationQuestion = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
            $purge = $helper->ask($input, $output, $confirmationQuestion);
        }

        if ($purge) {
            $ormPurger = new ORMPurger($this->entityManager);
            $ormPurger->purge();
        }

        $this->fixtureLoader->load();

        return Command::SUCCESS;
    }
}
