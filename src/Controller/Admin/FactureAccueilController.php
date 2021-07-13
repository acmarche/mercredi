<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use AcMarche\Mercredi\Facture\Form\FactureAttachType;
use AcMarche\Mercredi\Facture\Form\FactureEditType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_accueil")
 */
final class FactureAccueilController extends AbstractController
{
    private FactureAccueilRepository $factureAccueilRepository;
    private FactureHandler $factureHandler;
    private AccueilRepository $accueilRepository;

    public function __construct(
        FactureAccueilRepository $factureAccueilRepository,
        FactureHandler $factureHandler,
        AccueilRepository $accueilRepository
    ) {
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->factureHandler = $factureHandler;
        $this->accueilRepository = $accueilRepository;
    }

    /**
     * @Route("/{id}/attach", name="mercredi_admin_facture_accueil_attach", methods={"GET", "POST"})
     */
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $accueils = $this->accueilRepository->getAccueilsNonPayesByTuteurAndMonth($tuteur);

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
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_accueil_show", methods={"GET"})
     */
    public function show(FactureAccueil $factureAccueil): Response
    {
        $facture = $factureAccueil->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_accueil/show.html.twig',
            [
                'facture' => $facture,
                'factureAccueil' => $factureAccueil,
            ]
        );
    }

    /**
     * Route("/{id}/edit", name="mercredi_admin_facture_accueil_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //todo ?
            echo '';
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture/edit.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_accueil_delete", methods={"POST"})
     */
    public function delete(Request $request, FactureAccueil $factureAccueil): Response
    {
        if ($this->isCsrfTokenValid('delete'.$factureAccueil->getId(), $request->request->get('_token'))) {
            $facture = $factureAccueil->getFacture();
            $this->factureAccueilRepository->remove($factureAccueil);
            $this->factureAccueilRepository->flush();

            $this->addFlash('success', 'L\'accueil a bien été détaché');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
