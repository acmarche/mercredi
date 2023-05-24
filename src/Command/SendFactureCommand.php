<?php

namespace AcMarche\Mercredi\Command;

use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use DateTime;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'mercredi:facture-send',
    description: 'Envoie les factures par mail'
)]
class SendFactureCommand extends Command
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FactureCronRepository $factureCronRepository,
        private FactureEmailFactory $factureEmailFactory,
        private AdminEmailFactory $adminEmailFactory,
        private NotificationMailer $notificationMailer,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', null, InputOption::VALUE_OPTIONAL, 'Envoye facture déjà envoyée', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = (bool)$input->getOption('force');

        $io = new SymfonyStyle($input, $output);
        $crons = $this->factureCronRepository->findNotDone();
        foreach ($crons as $cron) {
            $i = 0;
            $factures = $this->factureRepository->findFacturesByMonth($cron->getMonth());
            $count = \count($factures);
            $io->writeln($count.' factures trouvées');

            $messageBase = $this->factureEmailFactory->messageFacture(
                $cron->getFromAdresse(),
                $cron->getSubject(),
                $cron->getBody()
            );

            foreach ($factures as $facture) {
                if (null !== $facture->getEnvoyeLe() && !$force) {
                    continue;
                }

                $messageFacture = clone $messageBase; //sinon attachs multiple

                $tuteur = $facture->getTuteur();
                $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);

                if (\count($emails) < 1) {
                    $error = 'Pas de mail pour la facture: '.$facture->getId();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                $this->factureEmailFactory->setTos($messageFacture, $emails);
                try {
                    $this->factureEmailFactory->attachFactureFromPath($messageFacture, $facture);
                } catch (Exception $e) {
                    $error = 'Pas de pièce jointe pour la facture: '.$facture->getId();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                try {
                    $this->notificationMailer->sendMail($messageFacture);
                } catch (TransportExceptionInterface $e) {
                    $error = 'Facture num '.$facture->getId().' '.$e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

                $facture->setEnvoyeA(implode(', ', $emails));
                $facture->setEnvoyeLe(new DateTime());
                ++$i;
                $io->writeln($i.'/'.$count);
                $this->factureRepository->flush();
            }
            $cron->setDone(true);
        }

        $this->factureRepository->flush();

        return Command::SUCCESS;
    }
}
