<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureComplement;
use AcMarche\Mercredi\Facture\Form\FactureComplementType;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_complement')]
final class FactureComplementController extends AbstractController
{
    public function __construct(
        private FactureComplementRepository $factureComplementRepository
    ) {
    }

    #[Route(path: '/{id}/new', name: 'mercredi_admin_facture_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Facture $facture): Response
    {
        $factureComplement = new FactureComplement($facture);
        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureComplementRepository->persist($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'Le complément a bien été ajouté');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
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

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_complement_show', methods: ['GET'])]
    public function show(FactureComplement $factureComplement): Response
    {
        $facture = $factureComplement->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_complement/show.html.twig',
            [
                'facture' => $facture,
                'factureComplement' => $factureComplement,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_facture_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureComplement $factureComplement): Response
    {
        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureComplementRepository->flush();
            $this->addFlash('success', 'Le complément a bien été modifié');

            return $this->redirectToRoute(
                'mercredi_admin_facture_complement_show',
                [
                    'id' => $factureComplement->getId(),
                ]
            );
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

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_facture_complement_delete', methods: ['POST'])]
    public function delete(Request $request, FactureComplement $factureComplement): RedirectResponse
    {
        $facture = $factureComplement->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureComplement->getId(), $request->request->get('_token'))) {
            $this->factureComplementRepository->remove($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'Le complément a bien été supprimé');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
