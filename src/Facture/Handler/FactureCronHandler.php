<?php

namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class FactureCronHandler
{
    public function __construct(
        private FactureCronRepository $factureCronRepository,
        private FactureRepository $factureRepository,
        private FactureEmailFactory $factureEmailFactory,
        private FactureFactory $factureFactory,
        private NotificationMailer $notificationMailer,
        private AdminEmailFactory $adminEmailFactory,
    ) {

    }

    public function execute(int $max = 50): array
    {
        $result = [];
        $crons = $this->factureCronRepository->findNotDone();

        $now = Carbon::now();
        foreach ($crons as $cron) {
            if ($cron->getDateLastSync()) {

                $lastSync = new Carbon($cron->getDateLastSync());

                if ($now->diffInMinutes($lastSync) < 60) {
                    $result[] = [
                        'message' => ' Last sync too early. Minutes: '.$now->diffInMinutes($lastSync),
                    ];
                    continue;
                }
            }

            $cron->setDateLastSync($now->modify('-5 minutes'));
            $this->factureCronRepository->flush();

            $factures = $this->factureRepository->findFacturesByMonthNotSendAndNotPaid($cron->getMonthDate());

            if (\count($factures) === 0) {
                $cron->setDone(true);
                $this->factureCronRepository->flush();
                $result[] = ['message' => $cron->getId().' 0 facture '];
                continue;
            }

            $messageBase = $this->factureEmailFactory->messageFacture(
                $cron->getFromAdresse(),
                $cron->getSubject(),
                $cron->getBody()
            );
            $i = 0;
            foreach ($factures as $facture) {
                $messageFacture = clone $messageBase; //sinon attachs multiple

                try {
                    $this->factureFactory->createOnePdf($facture, $cron->getMonthDate());
                } catch (\Exception $e) {
                    $error = 'Impossible de générer le pdf pour la facture: '.$facture->getId().' '.$e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert('Erreur pdf facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    $result[] = ['message' => $facture->getId().' error create pdf '.$e->getMessage()];
                    continue;
                }

                $tuteur = $facture->getTuteur();
                $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);

                if (\count($emails) < 1) {
                    $error = 'Pas de mail pour la facture: '.$facture->getId();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    $result[] = ['message' => $facture->getId().' error pas pdf '.$error];
                    continue;
                }

                $this->factureEmailFactory->setTos($messageFacture, $emails);

                try {
                    $this->factureEmailFactory->attachFactureFromPath($messageFacture, $facture);
                } catch (\Exception $e) {
                    $error = 'Pas de pièce jointe pour la facture: '.$facture->getId().' '.$e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    $result[] = ['message' => $facture->getId().' error attach '.$error];
                    continue;
                }

                try {
                    $this->notificationMailer->sendMail($messageFacture);
                } catch (TransportExceptionInterface $e) {
                    $error = 'Facture num '.$facture->getId().' '.$e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    $result[] = ['message' => $facture->getId().' error envoie '.$error];
                    continue;
                }

                $facture->setEnvoyeA(implode(', ', $emails));
                $facture->setEnvoyeLe(new \DateTime());
                $this->factureRepository->flush();
                ++$i;
                if ($i > $max) {
                    break;
                }
            }
            $result[] = ['message' => 'count '.$i];
        }

        return $result;
    }

    public function sendResult(array $result): void
    {
        $message = $this->adminEmailFactory->messageToJf('Result envoie facture', json_encode($result));
        $this->notificationMailer->sendAsEmailNotification($message);
    }
}