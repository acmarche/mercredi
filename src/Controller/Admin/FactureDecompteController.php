<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureDecompte;
use AcMarche\Mercredi\Facture\Form\FactureDecompteType;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/facture_decompte")
 */
final class FactureDecompteController extends AbstractController
{
    private FactureHandlerInterface $factureHandler;
    private FactureDecompteRepository $factureDecompteRepository;

    public function __construct(
        FactureDecompteRepository $factureDecompteRepository,
        FactureHandlerInterface $factureHandler
    ) {
        $this->factureHandler = $factureHandler;
        $this->factureDecompteRepository = $factureDecompteRepository;
    }

    /**
     * @Route("/{id}/new", name="mercredi_admin_facture_decompte_new", methods={"GET", "POST"})
     */
    public function new(Request $request, Facture $facture): Response
    {
        $factureDecompte = new FactureDecompte($facture);

        $form = $this->createForm(FactureDecompteType::class, $factureDecompte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->factureDecompteRepository->persist($factureDecompte);
            $this->factureDecompteRepository->flush();

            $this->addFlash('success', 'Le décompte a bien été ajouté');

            return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="mercredi_admin_facture_decompte_show", methods={"GET"})
     */
    public function show(FactureDecompte $factureDecompte): Response
    {
        $facture = $factureDecompte->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/show.html.twig',
            [
                'facture' => $facture,
                'factureDecompte' => $factureDecompte,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_facture_decompte_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, FactureDecompte $factureDecompte): Response
    {
        $form = $this->createForm(FactureDecompteType::class, $factureDecompte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureDecompteRepository->flush();
            $this->addFlash('success', 'Le décompte a bien été modifié');

            return $this->redirectToRoute('mercredi_admin_facture_decompte_show', ['id' => $factureDecompte->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/edit.html.twig',
            [
                'facture' => $factureDecompte->getFacture(),
                'factureDecompte' => $factureDecompte,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="mercredi_admin_facture_decompte_delete", methods={"POST"})
     */
    public function delete(Request $request, FactureDecompte $factureDecompte): Response
    {
        $facture = $factureDecompte->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureDecompte->getId(), $request->request->get('_token'))) {

            $this->factureDecompteRepository->remove($factureDecompte);
            $this->factureDecompteRepository->flush();

            $this->addFlash('success', 'Le décompte a bien été supprimé');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', ['id' => $facture->getId()]);
    }
}
