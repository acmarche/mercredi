<?php

namespace AcMarche\Mercredi\Command;

use Exception;
use DateTime;
use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendFactureCommand extends Command
{
    protected static $defaultName = 'mercredi:send-facture';
    protected static $defaultDescription = 'Envoie les factures par mail';

    private FactureRepository $factureRepository;
    private FactureEmailFactory $factureEmailFactory;
    private NotificationMailer $notificationMailer;
    private AdminEmailFactory $adminEmailFactory;
    private FactureCronRepository $factureCronRepository;

    public function __construct(
        FactureRepository $factureRepository,
        FactureCronRepository $factureCronRepository,
        FactureEmailFactory $factureEmailFactory,
        AdminEmailFactory $adminEmailFactory,
        NotificationMailer $notificationMailer,
        string $name = null
    ) {
        parent::__construct($name);
        $this->factureRepository = $factureRepository;
        $this->factureEmailFactory = $factureEmailFactory;
        $this->notificationMailer = $notificationMailer;
        $this->adminEmailFactory = $adminEmailFactory;
        $this->factureCronRepository = $factureCronRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('month', InputArgument::OPTIONAL, 'Mois format mm-yyyy')
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Envoye facture déjà envoyée', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = (bool)$input->getOption('force');
        /* $month = $input->getArgument('month');

         if (preg_match("#^\d{2}-\d{4}$#", $month) == false) {
             $message = $this->adminEmailFactory->messagAlert("Mauvais format de date", "Date: ".$month);
             $this->notificationMailer->sendAsEmailNotification($message);

             return Command::FAILURE;
         }*/

        $io = new SymfonyStyle($input, $output);
        $crons = $this->factureCronRepository->findNotDone();
        foreach ($crons as $cron) {
            $i = 0;
            $factures = $this->factureRepository->findFacturesByMonth($cron->getMonth());
            $count = count($factures);
            $io->writeln($count . ' factures trouvées');

            $messageBase = $this->factureEmailFactory->messageFacture(
                $cron->getFromAdresse(),
                $cron->getSubject(),
                $cron->getBody()
            );

            foreach ($factures as $facture) {
                if ($facture->getEnvoyeLe() != null && !$force) {
                    continue;
                }

                $messageFacture = clone $messageBase;//sinon attachs multiple

                $tuteur = $facture->getTuteur();
                $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);

                if (count($emails) < 1) {
                    $error = 'Pas de mail pour la facture: ' . $facture->getId();
                    $message = $this->adminEmailFactory->messageAlert("Erreur envoie facture", $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                $this->factureEmailFactory->setTos($messageFacture, $emails);
                try {
                    $this->factureEmailFactory->attachFactureFromPath($messageFacture, $facture);
                } catch (Exception $e) {
                    $error = 'Pas de pièce jointe pour la facture: ' . $facture->getId();
                    $message = $this->adminEmailFactory->messageAlert("Erreur envoie facture", $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                try {
                    $this->notificationMailer->sendMail($messageFacture);
                } catch (TransportExceptionInterface $e) {
                    $error = 'Facture num ' . $facture->getId() . ' ' . $e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert("Erreur envoie facture", $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                $facture->setEnvoyeA(implode(', ', $emails));
                $facture->setEnvoyeLe(new DateTime());
                $i++;
                $io->writeln($i . '/' . $count);
                $this->factureRepository->flush();
            }
            $cron->setDone(true);
        }

        $this->factureRepository->flush();

        return Command::SUCCESS;
    }
}
