<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Relation\Form\RelationType;
use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use AcMarche\Mercredi\Relation\Message\RelationUpdated;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/relation")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class RelationController extends AbstractController
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_relation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Relation $relation): Response
    {
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->relationRepository->flush();

            $this->dispatchMessage(new RelationUpdated($relation->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $relation->getEnfant()->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/relation/edit.html.twig',
            [
                'relation' => $relation,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/", name="mercredi_admin_relation_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $relationId = $request->request->get('relationid');

        if (!$relationId) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }
        $relation = $this->relationRepository->find($relationId);
        if (!$relation) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('mercredi_admin_home');
        }

        $enfant = $relation->getEnfant();

        if ($this->isCsrfTokenValid('delete'.$relation->getId(), $request->request->get('_token'))) {
            $this->relationRepository->remove($relation);
            $this->relationRepository->flush();
            $this->dispatchMessage(new RelationDeleted($relationId));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
    }
}
