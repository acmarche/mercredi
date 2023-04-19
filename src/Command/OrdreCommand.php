<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\OrdreService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:ordre',
)]
class OrdreCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private OrdreService $ordreService,
        private PresenceRepository $presenceRepository,
        private EnfantRepository $enfantRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('presence', InputArgument::REQUIRED, 'id presence');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $presenceId = $input->getArgument('presence');
        $presence = $this->presenceRepository->find($presenceId);

        if ($presence) {
            $this->io->writeln($presence->getEnfant());
            $this->io->writeln($presence->getJour()->getDateJour()->format('Y-m-d'));
            $ordre = $this->ordreService->getOrdreOnPresence($presence, true);
            $presents = $this->ordreService->getFratriesPresents($presence);
            foreach ($presents as $present) {
                $this->io->writeln('Présent: '.$present);
            }
            $this->io->writeln('Ordre '.$ordre);
            $this->io->writeln('Raison '.$this->ordreService->raison);
        }

        return Command::SUCCESS;
    }


}
