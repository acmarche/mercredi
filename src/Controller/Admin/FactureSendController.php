<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactoryTrait;
use AcMarche\Mercredi\Facture\Form\FactureSelectSendType;
use AcMarche\Mercredi\Facture\Form\FactureSendAllType;
use AcMarche\Mercredi\Facture\Form\FactureSendType;
use AcMarche\Mercredi\Facture\Mailer\FactureMailer;
use AcMarche\Mercredi\Facture\Message\FacturesCreated;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/factur/send")
 */
final class FactureSendController extends AbstractController
{
    private FactureRepository $factureRepository;
    private FactureMailer $factureMailer;
    private FacturePdfFactoryTrait $facturePdfFactory;

    public function __construct(
        FactureRepository $factureRepository,
        FactureMailer $factureMailer,
        FacturePdfFactoryTrait $facturePdfFactory
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureMailer = $factureMailer;
        $this->facturePdfFactory = $facturePdfFactory;
    }

    /**
     * @Route("/{id}/one", name="mercredi_admin_facture_send_one", methods={"GET","POST"})
     */
    public function sendOneFacture(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $data = $this->factureMailer->init($facture);
        $form = $this->createForm(FactureSendType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->factureMailer->sendFacture($facture, $data);
                $facture->setEnvoyeA($data['to']);
                $facture->setEnvoyeLe(new \DateTime());
                $this->addFlash('success', 'La facture a bien été envoyée');
                $this->factureRepository->flush();
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Une erreur est survenue: '.$e->getMessage());
            }

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
     * @Route("/all/mail/{month}", name="mercredi_admin_facture_send_all_by_mail", methods={"GET","POST"})
     */
    public function sendAllFacture(Request $request, string $month): Response
    {
        $form = $this->createForm(FactureSendAllType::class, $this->factureMailer->init());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $factures = $this->factureRepository->findFacturesByMonth($month);
            if (count($factures) === 0) {
                $this->addFlash('warning', 'Aucune facture trouvée pour ce mois');

                return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
            }

            foreach ($factures as $facture) {
                try {
                    $tuteur = $facture->getTuteur();
                    if (!$emails = TuteurUtils::getEmailsOfOneTuteur($tuteur)) {
                        $this->addFlash('danger', 'Pas de mail pour la facture: '.$facture->getId());
                        continue;
                    }
                    $data['to'] = count($emails) > 0 ? $emails[0] : null;
                    $this->factureMailer->sendFacture($facture, $data);
                    $this->addFlash('success', 'La facture a bien été envoyée');
                    $facture->setEnvoyeA($data['to']);
                    $facture->setEnvoyeLe(new \DateTime());
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', 'Une erreur est survenue: '.$e->getMessage());
                }
            }

            $this->factureRepository->flush();
            $this->dispatchMessage(new FacturesCreated($factures));

            return $this->redirectToRoute('mercredi_admin_facture_index');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/send_all.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/all/paper/{month}", name="mercredi_admin_facture_send_all_by_paper", methods={"GET","POST"})
     */
    public function sendAllFacturePapier(string $month): Response
    {
        $factures = $this->factureRepository->findFacturesByMonthOnlyPaper($month);
        if (count($factures) === 0) {
            $this->addFlash('warning', 'Aucune facture trouvée pour ce mois au format papier');

            return $this->redirectToRoute('mercredi_admin_facture_send_select_month');
        }

        return $this->facturePdfFactory->generates($factures, $month);
    }
}