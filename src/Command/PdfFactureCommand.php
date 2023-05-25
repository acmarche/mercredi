<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'mercredi:facture-pdf',
    description: 'Génère les pdf des factures'
)]
class PdfFactureCommand extends Command
{
    public function __construct(
        private FactureRepository $factureRepository,
        private AdminEmailFactory $adminEmailFactory,
        private NotificationMailer $notificationMailer,
        private FactureFactory $factureFactory,

    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('month', InputArgument::REQUIRED, 'Mois format mm-yyyy');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $month = $input->getArgument('month');

        if (preg_match("#^\d{2}-\d{4}$#", $month) == false) {
            $io->error("Mauvais format de date: mm-yyyy");
            $message = $this->adminEmailFactory->messageAlert("Mauvais format de date", "Date: ".$month);
            $this->notificationMailer->sendAsEmailNotification($message);

            return Command::FAILURE;
        }

        $factures = $this->factureRepository->findFacturesByMonth($month);
        try {
            $finish = $this->factureFactory->createAllPdf($factures, $month);
        } catch (Exception $e) {
            $io->error('Erreur survenue: '.$e->getMessage());

            return Command::FAILURE;
        }
        if ($finish) {
            $io->success('La demande d\'envoie des factures a bien été programmée.');

            return Command::SUCCESS;

        }

        return Command::SUCCESS;
    }
}
