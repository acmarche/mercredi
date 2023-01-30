<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use AcMarche\Mercredi\Scolaire\Form\AnneeScolaireType;
use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireCreated;
use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireDeleted;
use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireUpdated;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/annee_scolaire')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AnneeScolaireController extends AbstractController
{
    public function __construct(
        private AnneeScolaireRepository $anneeScolaireRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_annee_scolaire_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/annee_scolaire/index.html.twig',
            [
                'annees' => $this->anneeScolaireRepository->findAllOrderByOrdre(),
            ]
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_annee_scolaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $anneeScolaire = new AnneeScolaire();
        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->anneeScolaireRepository->persist($anneeScolaire);
            $this->anneeScolaireRepository->flush();

            $this->dispatcher->dispatch(new AnneeScolaireCreated($anneeScolaire->getId()));

            return $this->redirectToRoute('mercredi_admin_annee_scolaire_show', [
                'id' => $anneeScolaire->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/annee_scolaire/new.html.twig',
            [
                'annee' => $anneeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_annee_scolaire_show', methods: ['GET'])]
    public function show(AnneeScolaire $anneeScolaire): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/annee_scolaire/show.html.twig',
            [
                'annee_scolaire' => $anneeScolaire,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_annee_scolaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnneeScolaire $anneeScolaire): Response
    {
        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->anneeScolaireRepository->flush();

            $this->dispatcher->dispatch(new AnneeScolaireUpdated($anneeScolaire->getId()));

            return $this->redirectToRoute('mercredi_admin_annee_scolaire_show', [
                'id' => $anneeScolaire->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/annee_scolaire/edit.html.twig',
            [
                'annee_scolaire' => $anneeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_annee_scolaire_delete', methods: ['POST'])]
    public function delete(Request $request, AnneeScolaire $anneeScolaire): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$anneeScolaire->getId(), $request->request->get('_token'))) {
            if (\count($anneeScolaire->getEnfants()) > 0) {
                $this->addFlash('danger', 'Une année scolaire contenant des enfants ne peux pas être supprimée');

                return $this->redirectToRoute('mercredi_admin_annee_scolaire_show', [
                    'id' => $anneeScolaire->getId(),
                ]);
            }

            $ecoleId = $anneeScolaire->getId();
            $this->anneeScolaireRepository->remove($anneeScolaire);
            $this->anneeScolaireRepository->flush();
            $this->dispatcher->dispatch(new AnneeScolaireDeleted($ecoleId));
        }

        return $this->redirectToRoute('mercredi_admin_annee_scolaire_index');
    }
}
