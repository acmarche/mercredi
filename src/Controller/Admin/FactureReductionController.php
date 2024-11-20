<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureReduction;
use AcMarche\Mercredi\Facture\Form\FactureReductionType;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_reduction')]
final class FactureReductionController extends AbstractController
{
    public function __construct(
        private FactureReductionRepository $factureReductionRepository,
    ) {}

    #[Route(path: '/{id}/new', name: 'mercredi_admin_facture_reduction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Facture $facture): Response
    {
        if (null !== $facture->getEnvoyeLe()) {
            $this->addFlash('danger', 'La facture a déjà été envoyée');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }
        $factureReduction = new FactureReduction($facture);
        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureReductionRepository->persist($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été ajoutée');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/show', name: 'mercredi_admin_facture_reduction_show', methods: ['GET'])]
    public function show(FactureReduction $factureReduction): Response
    {
        $facture = $factureReduction->getFacture();

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/show.html.twig',
            [
                'facture' => $facture,
                'factureReduction' => $factureReduction,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_facture_reduction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureReduction $factureReduction): Response
    {
        if (null !== $factureReduction->getFacture()->getEnvoyeLe()) {
            $this->addFlash('danger', 'La facture a déjà été envoyée');

            return $this->redirectToRoute('mercredi_admin_facture_show', [
                'id' => $factureReduction->getFacture()->getId(),
            ]);
        }
        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureReductionRepository->flush();
            $this->addFlash('success', 'La réduction a bien été modifiée');

            return $this->redirectToRoute(
                'mercredi_admin_facture_reduction_show',
                [
                    'id' => $factureReduction->getId(),
                ],
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/facture_reduction/edit.html.twig',
            [
                'facture' => $factureReduction->getFacture(),
                'factureReduction' => $factureReduction,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_facture_reduction_delete', methods: ['POST'])]
    public function delete(Request $request, FactureReduction $factureReduction): RedirectResponse
    {
        $facture = $factureReduction->getFacture();
        if ($this->isCsrfTokenValid('delete'.$factureReduction->getId(), $request->request->get('_token'))) {
            $this->factureReductionRepository->remove($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été supprimée');
        }

        return $this->redirectToRoute('mercredi_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
