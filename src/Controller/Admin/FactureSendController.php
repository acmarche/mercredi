<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureCron;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Facture\Form\FactureSelectSendType;
use AcMarche\Mercredi\Facture\Form\FactureSendAllType;
use AcMarche\Mercredi\Facture\Form\FactureSendType;
use AcMarche\Mercredi\Facture\Repository\FactureCronRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Mailer\Factory\AdminEmailFactory;
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture/send')]
final class FactureSendController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FactureCronRepository $factureCronRepository,
        private FacturePdfFactoryTrait $facturePdfFactory,
        private FactureEmailFactory $factureEmailFactory,
        private NotificationMailer $notificationMailer,
        private FactureFactory $factureFactory,
        private AdminEmailFactory $adminEmailFactory
    ) {
    }

    #[Route(path: '/select/month', name: 'mercredi_admin_facture_send_select_month', methods: ['GET', 'POST'])]
    public function selectMonth(Request $request): Response
    {
        $form = $this->createForm(FactureSelectSendType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mois = $form->get('mois')->getData();
            $mode = $form->get('mode')->getData();
            if ('mail' === $mode) {
                return $this->redirectToRoute('mercredi_admin_facture_send_all_by_mail', [
                    'month' => $mois,
                ]);
            }
            if ('papier' === $mode) {
                return $this->redirectToRoute('mercredi_admin_facture_send_all_by_paper', [
                    'month' => $mois,
                ]);
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/select_month.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/one', name: 'mercredi_admin_facture_send_one', methods: ['GET', 'POST'])]
    public function sendOneFacture(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $args = $this->factureEmailFactory->initFromAndToForForm($facture);
        $form = $this->createForm(FactureSendType::class, $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $message = $this->factureEmailFactory->messageFacture($data['from'], $data['sujet'], $data['texte']);
            $this->factureEmailFactory->setTos($message, [$data['to']]);
            $this->factureEmailFactory->attachFactureOnTheFly($facture, $message);

            $this->notificationMailer->sendAsEmailNotification($message);
            $facture->setEnvoyeA($data['to']);
            $facture->setEnvoyeLe(new DateTime());
            $this->addFlash('success', 'La facture a bien été envoyée');
            $this->factureRepository->flush();

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/send_one.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/all/mail/{month}', name: 'mercredi_admin_facture_send_all_by_mail', methods: ['GET', 'POST'])]
    public function sendAllFacture(Request $request, string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth($month);
        $form = $this->createForm(FactureSendAllType::class, $this->factureEmailFactory->initFromAndToForForm());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ([] === $factures) {
                $this->addFlash('warning', 'Aucune facture trouvée pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
            }

            if (($cron = $this->factureCronRepository->findOneByMonth($month)) !== null) {
                $cron->setFromAdresse($data['from']);
                $cron->setSubject($data['sujet']);
                $cron->setBody($data['texte']);
            } else {
                $cron = new FactureCron($data['from'], $data['sujet'], $data['texte'], $month);
                $this->factureCronRepository->persist($cron);
            }
            $this->factureCronRepository->flush();

            return $this->redirectToRoute(
                'mercredi_admin_facture_create_pdf_all',
                [
                    'month' => $month,
                    'pause' => 0,
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/send_all.html.twig',
            [
                'form' => $form->createView(),
                'factures' => $factures,
                'month' => $month,
            ]
        );
    }

    #[Route(path: '/all/pdf/{month}/{pause}', name: 'mercredi_admin_facture_create_pdf_all', methods: ['GET'])]
    public function pdfAll(string $month, int $pause): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth($month);
        if (null === $this->factureCronRepository->findOneByMonth($month)) {
            $this->addFlash('danger', 'Erreur aucun cron trouvé');

            return $this->redirectToRoute('mercredi_admin_facture_index');
        }
        if (1 === $pause) {
            return $this->render(
                '@AcMarcheMercrediAdmin/facture/create_pdf.html.twig',
                [
                    'pause' => $pause,
                    'month' => $month,
                    'finish' => false,
                ]
            );
        }
        try {
            $finish = $this->factureFactory->createAllPdf($factures, $month);
        } catch (Exception $e) {
            $this->addFlash('danger', 'Erreur survenue: '.$e->getMessage());

            return $this->redirectToRoute('mercredi_admin_facture_send_all_by_mail', [
                'month' => $month,
            ]);
        }
        if ($finish) {
            $this->addFlash(
                'success',
                'La demande d\'envoie des factures a bien été programmée.'
            );
        } else {
            return $this->redirectToRoute(
                'mercredi_admin_facture_create_pdf_all',
                [
                    'month' => $month,
                    'pause' => 1,
                ]
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/create_pdf.html.twig',
            [
                'pause' => $pause,
                'month' => $month,
                'finish' => $finish,
            ]
        );
    }

    #[Route(path: '/all/sending/{month}/{pause}', name: 'mercredi_admin_facture_sending_all', methods: ['GET'])]
    public function sendAll(string $month, int $pause): Response
    {
        $i = 0;
        $cron = $this->factureCronRepository->findOneByMonth($month);
        $factures = $this->factureRepository->findFacturesByMonthNotSend($month);
        $count = \count($factures);
        $finish = 0 === $count;
        $messageBase = $this->factureEmailFactory->messageFacture(
            $cron->getFromAdresse(),
            $cron->getSubject(),
            $cron->getBody()
        );
        foreach ($factures as $facture) {
            $messageFacture = clone $messageBase; //sinon attachs multiple

            $tuteur = $facture->getTuteur();
            $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);

            if (\count($emails) < 1) {
                $error = 'Pas de mail pour la facture: '.$facture->getId();
                $this->addFlash('danger', $error);
                $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
                $this->notificationMailer->sendAsEmailNotification($message);
                continue;
            }

            $this->factureEmailFactory->setTos($messageFacture, $emails);
            try {
                $this->factureEmailFactory->attachFactureFromPath($messageFacture, $facture);
            } catch (Exception $e) {
                $error = 'Pas de pièce jointe pour la facture: '.$facture->getId();
                $this->addFlash('danger', $error);
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
            $this->factureRepository->flush();
            $pause = 1;
            if ($i > 30) {
                break;
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/sending_all.html.twig',
            [
                'pause' => $pause,
                'month' => $month,
                'finish' => $finish,
                'count' => $count,
            ]
        );
    }

    #[Route(path: '/all/paper/{month}', name: 'mercredi_admin_facture_send_all_by_paper', methods: ['GET'])]
    public function facturesPapier(string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonthOnlyPaper($month);
        if ([] === $factures) {
            $this->addFlash('warning', 'Aucune facture trouvée pour ce mois au format papier');

            return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/paper.html.twig',
            [
                'factures' => $factures,
                'month' => $month,
            ]
        );
    }

    #[Route(path: '/download/paper/{month}', name: 'mercredi_admin_facture_send_download_by_paper', methods: ['GET'])]
    public function downloadFacturePapier(string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonthOnlyPaper($month);
        if ([] === $factures) {
            $this->addFlash('warning', 'Aucune facture trouvée pour ce mois au format papier');

            return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
        }

        return $this->facturePdfFactory->generates($factures, $month);
    }
}
