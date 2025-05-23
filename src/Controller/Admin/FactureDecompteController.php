<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureDecompte;
use AcMarche\Mercredi\Facture\Form\FactureDecompteType;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_decompte')]
final class FactureDecompteController extends AbstractController
{
    public function __construct(
        private FactureDecompteRepository $factureDecompteRepository,
    ) {}

    #[Route(path: '/{id}/new', name: 'mercredi_admin_facture_decompte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Facture $facture): Response
    {
        $factureDecompte = new FactureDecompte($facture);
        $form = $this->createForm(FactureDecompteType::class, $factureDecompte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureDecompteRepository->persist($factureDecompte);
            $this->factureDecompteRepository->flush();

            $this->addFlash('success', 'Le décompte a bien été ajouté');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_decompte_show', methods: ['GET'])]
    public function show(FactureDecompte $factureDecompte): Response
    {
        $facture = $factureDecompte->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/show.html.twig',
            [
                'facture' => $facture,
                'factureDecompte' => $factureDecompte,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_facture_decompte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureDecompte $factureDecompte): Response
    {
        $form = $this->createForm(FactureDecompteType::class, $factureDecompte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureDecompteRepository->flush();
            $this->addFlash('success', 'Le décompte a bien été modifié');

            return $this->redirectToRoute('mercredi_admin_facture_decompte_show', [
                'id' => $factureDecompte->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_decompte/edit.html.twig',
            [
                'facture' => $factureDecompte->getFacture(),
                'factureDecompte' => $factureDecompte,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_facture_decompte_delete', methods: ['POST'])]
    public function delete(Request $request, FactureDecompte $factureDecompte): RedirectResponse
    {
        $facture = $factureDecompte->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureDecompte->getId(), $request->request->get('_token'))) {
            $this->factureDecompteRepository->remove($factureDecompte);
            $this->factureDecompteRepository->flush();

            $this->addFlash('success', 'Le décompte a bien été supprimé');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
