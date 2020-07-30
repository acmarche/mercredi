<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends Command
{
    protected static $defaultName = 'mercredi:migration';
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;


    public function __construct(
        EnfantRepository $enfantRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->enfantRepository = $enfantRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Migration uuid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->enfantRepository->findAll() as $enfant) {
            $enfant->generateUuid();
        }

        $this->enfantRepository->flush();

        $io->success('User crée.');

        return 0;
    }
}
