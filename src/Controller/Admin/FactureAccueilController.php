<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_accueil")
 */
final class FactureAccueilController extends AbstractController
{
    private FactureHandler $factureHandler;
    private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository;

    public function __construct(
        FactureHandler $factureHandler,
        FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository
    ) {
        $this->factureHandler = $factureHandler;
        $this->facturePresenceNonPayeRepository = $facturePresenceNonPayeRepository;
    }

    /**
     * @Route("/{id}/attach", name="mercredi_admin_facture_accueil_attach", methods={"GET", "POST"})
     */
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);

        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accueilsF = $request->request->get('accueils', []);
            $this->factureHandler->handleManually($facture, [], $accueilsF);

            $this->addFlash('success', 'Les accueils ont bien été attachés');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_accueil/attach.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }
}
