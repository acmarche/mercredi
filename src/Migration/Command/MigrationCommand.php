<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrationCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'mercredi:migration';
    private EnfantRepository $enfantRepository;

    public function __construct(
        EnfantRepository $enfantRepository,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->enfantRepository = $enfantRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migration uuid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        foreach ($this->enfantRepository->findAll() as $enfant) {
            $enfant->generateUuid();
            $enfant->generateSlug();
        }

        $this->enfantRepository->flush();

        $symfonyStyle->success('User crée.');

        return 0;
    }
}
