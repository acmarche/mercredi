<?php

namespace AcMarche\Mercredi\Migration\Command;

use AcMarche\Mercredi\Migration\Handler\EnfantImport;
use AcMarche\Mercredi\Migration\Handler\ParametreImport;
use AcMarche\Mercredi\Migration\Handler\TuteurImport;
use AcMarche\Mercredi\Migration\Handler\UserImport;
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
    private ParametreImport $parametreImport;
    private EnfantImport $enfantImport;
    private TuteurImport $tuteurImport;
    private UserImport $userImport;

    public function __construct(
        ParametreImport $parametreImport,
        UserImport $userImport,
        TuteurImport $tuteurImport,
        EnfantImport $enfantImport,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->parametreImport = $parametreImport;
        $this->enfantImport = $enfantImport;
        $this->tuteurImport = $tuteurImport;
        $this->userImport = $userImport;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migration uuid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $this->parametreImport->setIo($symfonyStyle);
        //    $this->parametreImport->importAll();
      //  $this->userImport->import($symfonyStyle);
        //  $this->tuteurImport->import($symfonyStyle);

        //   $this->enfantRepository->flush();


        return Command::SUCCESS;
    }

}
