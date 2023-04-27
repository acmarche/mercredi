<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Form\AddChildAutocompleteType;
use AcMarche\Mercredi\Relation\Form\RelationType;
use AcMarche\Mercredi\Relation\Message\RelationCreated;
use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use AcMarche\Mercredi\Relation\Message\RelationUpdated;
use AcMarche\Mercredi\Relation\RelationHandler;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/relation')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class RelationController extends AbstractController
{
    public function __construct(
        private RelationRepository $relationRepository,
        private RelationHandler $relationHandler,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/attach/enfant/{id}', name: 'mercredi_admin_relation_attach_enfant', methods: ['POST'])]
    public function attachEnfant(Request $request, Tuteur $tuteur): RedirectResponse
    {
        $form = $this->createForm(AddChildAutocompleteType::class, null, [
            'action' => $this->generateUrl('mercredi_admin_relation_attach_enfant', ['id' => $tuteur->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $enfant = $data['nom'];
            if (!$enfant) {
                $this->addFlash('danger', 'Enfant non trouvé');
            } else {
                try {
                    $relation = $this->relationHandler->handleAttachEnfant($tuteur, $enfant->getId());
                    $this->dispatcher->dispatch(new RelationCreated($relation->getId()));
                } catch (Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_relation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Relation $relation): Response
    {
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->relationRepository->flush();

            $this->dispatcher->dispatch(new RelationUpdated($relation->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', [
                'id' => $relation->getEnfant()->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/relation/edit.html.twig',
            [
                'relation' => $relation,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/delete', name: 'mercredi_admin_relation_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $relationId = $request->request->get('relationid');
        if (!$relationId) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }
        $relation = $this->relationRepository->find($relationId);
        if (!$relation instanceof Relation) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }
        $tuteur = $relation->getTuteur();
        if ($this->isCsrfTokenValid('delete'.$relation->getId(), $request->request->get('_token'))) {
            $this->relationRepository->remove($relation);
            $this->relationRepository->flush();
            $this->dispatcher->dispatch(new RelationDeleted($relationId));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }
}
