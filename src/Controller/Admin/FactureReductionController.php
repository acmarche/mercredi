<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureReduction;
use AcMarche\Mercredi\Facture\Form\FactureReductionType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_reduction")
 */
final class FactureReductionController extends AbstractController
{
    private FactureHandler $factureHandler;
    private FactureReductionRepository $factureReductionRepository;

    public function __construct(
        FactureReductionRepository $factureReductionRepository,
        FactureHandler $factureHandler
    ) {
        $this->factureHandler = $factureHandler;
        $this->factureReductionRepository = $factureReductionRepository;
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_facture_reduction_new", methods={"GET", "POST"})
     */
    public function new(Request $request, Facture $facture): Response
    {
        $factureReduction = new FactureReduction($facture);

        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->factureReductionRepository->persist($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été ajoutée');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_reduction_show", methods={"GET"})
     */
    public function show(FactureReduction $factureReduction): Response
    {
        $facture = $factureReduction->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/show.html.twig',
            [
                'facture' => $facture,
                'facturePresence' => $factureReduction,
            ]
        );
    }

    /**
     * Route("/{id}/edit", name="mercredi_admin_facture_reduction_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, FactureReduction $factureReduction): Response
    {
        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->factureReductionRepository->flush();
            $this->addFlash('success', 'La réduction a bien été modifiée');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/edit.html.twig',
            [
                'facture' => $factureReduction->getFacture(),
                'factureReduction' => $factureReduction,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_reduction_delete", methods={"POST"})
     */
    public function delete(Request $request, FactureReduction $factureReduction): Response
    {
        $facture = $factureReduction->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureReduction->getId(), $request->request->get('_token'))) {

            $this->factureReductionRepository->remove($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été supprimée');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
