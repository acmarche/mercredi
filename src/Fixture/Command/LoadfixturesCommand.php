<?php

namespace AcMarche\Mercredi\Fixture\Command;

use AcMarche\Mercredi\Fixture\FixtureLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:load-fixtures',
    description: 'Chargment des fixtures'
)]
final class LoadfixturesCommand extends Command
{
    public function __construct(
        private FixtureLoader $fixtureLoader,
        private EntityManagerInterface $entityManager,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $purge = $input->getOption('purge');

        if (null === $purge) {
            $confirmationQuestion = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
            $purge = $helper->ask($input, $output, $confirmationQuestion);
        }

        if ($purge) {
            $ormPurger = new ORMPurger($this->entityManager);
            $ormPurger->setPurgeMode(1);
            $ormPurger->purge();
            $io = new SymfonyStyle($input, $output);
            $io->info('Bd purgée');
        }

        $this->fixtureLoader->load();

        return Command::SUCCESS;
    }
}
