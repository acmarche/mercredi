<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:fix',
)]
class FixCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly TuteurRepository $tuteurRepository,
        private readonly PresenceRepository $presenceRepository,
        private readonly AccueilRepository $accueilRepository,
        private readonly FactureRepository $factureRepository,
        private readonly FactureHandlerInterface $factureHandler,
        private readonly FactureCalculatorInterface $factureCalculator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('check', "check", InputOption::VALUE_NONE, 'Check');
        $this->addOption('flush', "flush", InputOption::VALUE_NONE, 'Flush');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        foreach ($this->factureRepository->findFacturesByMonth('10-2025') as $facture) {
            // $facture = $this->factureRepository->find(12629);
            if ($facture->getMois() == '10-2025') {
                $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
                if ($facture->factureDetailDto->total === 0.0) {
                    $tuteurFullName = $facture->getTuteur()->getNom().' '.$facture->getTuteur()->getPrenom();
                    $this->io->error('Facture '.$facture->getId().' '.$tuteurFullName.' est nulle');
                    $this->factureRepository->remove($facture);
                }
            }
        }

        $this->factureRepository->flush();

        return Command::SUCCESS;
    }

    private function missOctobre(): void
    {
        $dateSeptembre = new \DateTime('2025-09-01');
        $dateOctobre = new \DateTime('2025-10-01');

        foreach ($this->tuteurRepository->findAll() as $tuteur) {
            $accueils = $this->accueilRepository->findByTuteurAndMonth($tuteur, $dateSeptembre);
            $presences = $this->presenceRepository->findByTuteurAndMonth($tuteur, $dateSeptembre);
            $accueilsNotAttached = $presencesNotAttached = [];
            foreach ($accueils as $accueil) {
                if (!$this->facturePresenceRepository->findByAccueil($accueil)) {
                    $accueilsNotAttached[] = $accueil;
                }
            }
            foreach ($presences as $presence) {
                if (!$this->facturePresenceRepository->findByPresence($presence)) {
                    $presencesNotAttached[] = $presence;
                }
            }

            if (count($accueilsNotAttached) > 0 || count($presencesNotAttached) > 0) {
                $this->io->title($tuteur->getNom().' '.$tuteur->getPrenom());
                $factureOctobre = $this->factureRepository->findByTuteurAndMonth($tuteur, $dateOctobre);
                if (!$factureOctobre) {
                    $factureOctobre = $this->factureHandler->generateByMonthForTuteur($tuteur, '10-2025');
                    // continue;
                    $this->io->error('Pas de facture octobre');
                }
                foreach ($accueilsNotAttached as $accueil) {
                    $this->io->writeln(
                        $accueil->getDateJour()->format('Y-m-d').' '.$accueil->getEnfant()->getPrenom().' Accueil'
                    );
                }
                foreach ($presencesNotAttached as $presence) {
                    $this->io->writeln(
                        $presence->getJour()->getDateJour()->format('Y-m-d').' '.$presence->getEnfant()->getPrenom(
                        ).' Presence'
                    );
                }
                $this->factureHandler->attachAccueils($factureOctobre, $accueilsNotAttached);
                $this->factureHandler->attachPresences($factureOctobre, $presencesNotAttached);
            }

            $this->factureRepository->flush();
        }
    }

}
