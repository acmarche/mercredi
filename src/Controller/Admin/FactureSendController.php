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
use AcMarche\Mercredi\Mailer\Factory\FactureEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture/send")
 */
final class FactureSendController extends AbstractController
{
    private FactureRepository $factureRepository;
    private FacturePdfFactoryTrait $facturePdfFactory;
    private FactureEmailFactory $factureEmailFactory;
    private NotificationMailer $notificationMailer;
    private FactureCronRepository $factureCronRepository;
    private FactureFactory $factureFactory;

    public function __construct(
        FactureRepository $factureRepository,
        FactureCronRepository $factureCronRepository,
        FacturePdfFactoryTrait $facturePdfFactory,
        FactureEmailFactory $factureEmailFactory,
        NotificationMailer $notificationMailer,
        FactureFactory $factureFactory
    ) {
        $this->factureRepository = $factureRepository;
        $this->facturePdfFactory = $facturePdfFactory;
        $this->factureEmailFactory = $factureEmailFactory;
        $this->notificationMailer = $notificationMailer;
        $this->factureCronRepository = $factureCronRepository;
        $this->factureFactory = $factureFactory;
    }

    /**
     * @Route("/select/month", name="mercredi_admin_facture_send_select_month", methods={"GET","POST"})
     */
    public function selectMonth(Request $request): Response
    {
        $form = $this->createForm(FactureSelectSendType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mois = $form->get('mois')->getData();
            $mode = $form->get('mode')->getData();
            if ($mode === 'mail') {
                return $this->redirectToRoute('mercredi_admin_facture_send_all_by_mail', ['month' => $mois]);
            }
            if ($mode === 'papier') {
                return $this->redirectToRoute('mercredi_admin_facture_send_all_by_paper', ['month' => $mois]);
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/select_month.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/one", name="mercredi_admin_facture_send_one", methods={"GET","POST"})
     */
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
            $this->factureEmailFactory->attachFactureOnTheFly($message, $facture);

            $this->notificationMailer->sendAsEmailNotification($message);
            $facture->setEnvoyeA($data['to']);
            $facture->setEnvoyeLe(new \DateTime());
            $this->addFlash('success', 'La facture a bien été envoyée');
            $this->factureRepository->flush();

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
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

    /**
     * @Route("/all/mail/{month}", name="mercredi_admin_facture_send_all_by_mail", methods={"GET","POST"})
     */
    public function sendAllFacture(Request $request, string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth($month);
        $form = $this->createForm(FactureSendAllType::class, $this->factureEmailFactory->initFromAndToForForm());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (count($factures) === 0) {
                $this->addFlash('warning', 'Aucune facture trouvée pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
            }

            if ($cron = $this->factureCronRepository->findOneByMonth($month)) {
                $cron->setFromAdresse($data['from']);
                $cron->setSubject($data['sujet']);
                $cron->setBody($data['texte']);
            } else {
                $cron = new FactureCron($data['from'], $data['sujet'], $data['texte'], $month);
                $this->factureCronRepository->persist($cron);
            }
            $this->factureCronRepository->flush();

            return $this->redirectToRoute('mercredi_admin_facture_create_pdf_all', ['month' => $month, 'pause' => 0]
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

    /**
     * @Route("/all/pdf/{month}/{pause}", name="mercredi_admin_facture_create_pdf_all", methods={"GET"})
     */
    public function pdfAll(string $month, int $pause): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth($month);

        if (!$cron = $this->factureCronRepository->findOneByMonth($month)) {
            $this->addFlash('danger', 'Erreur aucun cron trouvé');

            return $this->redirectToRoute('mercredi_admin_facture_index');
        }
        if ($pause == 1) {
            return $this->render(
                '@AcMarcheMercrediAdmin/facture/create_pdf.html.twig',
                [
                    'pause' => $pause,
                    'month' => $month,
                    'finish' => false,
                ]
            );
        }
        $finish = $this->factureFactory->createAllPdf($factures);
        if ($finish) {
            $this->addFlash(
                'success',
                'La demande d\'envoie des factures a bien été programmée. Vous pouvez quitter cette page'
            );
        } else {
            return $this->redirectToRoute('mercredi_admin_facture_create_pdf_all', ['month' => $month, 'pause' => 1]
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

    /**
     * @Route("/all/paper/{month}", name="mercredi_admin_facture_send_all_by_paper", methods={"GET"})
     */
    public function facturesPapier(string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonthOnlyPaper($month);
        if (count($factures) === 0) {
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

    /**
     * @Route("/download/paper/{month}", name="mercredi_admin_facture_send_download_by_paper", methods={"GET"})
     */
    public function downloadFacturePapier(string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonthOnlyPaper($month);
        if (count($factures) === 0) {
            $this->addFlash('warning', 'Aucune facture trouvée pour ce mois au format papier');

            return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
        }

        return $this->facturePdfFactory->generates($factures, $month);
    }
}
