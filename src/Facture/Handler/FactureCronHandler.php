<?php

namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
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

    public function execute(int $max = 50): void
    {
        if (!$crons = $this->factureCronRepository->findNotDone()) {
            return;
        }

        $now = new \DateTime();
        foreach ($crons as $cron) {
            if ($cron->getDateLastSync()) {
                if ($now->format('G') > $cron->getDateLastSync()->format('G')) {
                    return;
                }
            }

            $cron->setDateLastSync($now);
            $this->factureCronRepository->flush();

            $factures = $this->factureRepository->findFacturesByMonthNotSend($cron->getMonth());
            if (\count($factures) === 0) {
                $cron->setDone(true);
                $this->factureCronRepository->flush();
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
                    $this->factureFactory->createOnePdf($facture, $cron->getMonth());
                } catch (\Exception $e) {
                    $error = 'Impossible de générer le pdf pour la facture: '.$facture->getId().' '.$e->getMessage();
                    $message = $this->adminEmailFactory->messageAlert('Erreur pdf facture', $error);
                    $this->notificationMailer->sendAsEmailNotification($message);
                    continue;
                }

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
                } catch (\Exception $e) {
                    $error = 'Pas de pièce jointe pour la facture: '.$facture->getId().' '.$e->getMessage();
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
                $facture->setEnvoyeLe(new \DateTime());
                $this->factureRepository->flush();

                ++$i;
                if ($i > $max) {
                    break;
                }
            }
        }
    }
}