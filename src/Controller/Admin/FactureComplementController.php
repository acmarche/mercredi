<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureComplement;
use AcMarche\Mercredi\Facture\Form\FactureComplementType;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_complement")
 */
final class FactureComplementController extends AbstractController
{
    private FactureHandler $factureHandler;
    private FactureComplementRepository $factureComplementRepository;

    public function __construct(
        FactureComplementRepository $factureComplementRepository,
        FactureHandler $factureHandler
    ) {
        $this->factureHandler = $factureHandler;
        $this->factureComplementRepository = $factureComplementRepository;
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_facture_complement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, Facture $facture): Response
    {
        $factureComplement = new FactureComplement($facture);

        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->factureComplementRepository->persist($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'La réduction a bien été ajoutée');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_complement/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_complement_show", methods={"GET"})
     */
    public function show(FactureComplement $factureComplement): Response
    {
        $facture = $factureComplement->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_complement/show.html.twig',
            [
                'facture' => $facture,
                'facturePresence' => $factureComplement,
            ]
        );
    }

    /**
     * Route("/{id}/edit", name="mercredi_admin_facture_complement_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, FactureComplement $factureComplement): Response
    {
        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->factureComplementRepository->flush();
            $this->addFlash('success', 'La réduction a bien été modifiée');
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_complement/edit.html.twig',
            [
                'facture' => $factureComplement->getFacture(),
                'factureComplement' => $factureComplement,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_complement_delete", methods={"POST"})
     */
    public function delete(Request $request, FactureComplement $factureComplement): Response
    {
        $facture = $factureComplement->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureComplement->getId(), $request->request->get('_token'))) {

            $this->factureComplementRepository->remove($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'La réduction a bien été supprimée');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
