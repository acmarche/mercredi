<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Admin\Entity\Reduction;
use AcMarche\Mercredi\Admin\Form\ReductionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Reduction controller.
 *
 * @Route("/reduction")
 * @IsGranted("ROLE_MERCREDI_READ")
 */
class ReductionController extends AbstractController
{
    /**
     * Lists all Reduction entities.
     *
     * @Route("/", name="reduction", methods={"GET"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(Reduction::class)->search();

        return $this->render(
            'admin/reduction/index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * Creates a form to create a Reduction entity.
     *
     * @param Reduction $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createCreateForm(Reduction $entity)
    {
        $form = $this->createForm(
            ReductionType::class,
            $entity,
            [
                'action' => $this->generateUrl('reduction_new'),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }

    /**
     * Displays a form to create a new Reduction entity.
     *
     * @Route("/new", name="reduction_new", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function new(Request $request)
    {
        $entity = new Reduction();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            $entity->setUserAdd($user);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'La r??duction a bien ??t?? ajout??e');

            return $this->redirectToRoute('reduction');
        }

        return $this->render(
            'admin/reduction/new.html.twig',
            [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Reduction entity.
     *
     * @Route("/{slugname}", name="reduction_show", methods={"GET"})
     */
    public function show(Reduction $reduction)
    {
        $deleteForm = $this->createDeleteForm($reduction->getId());
        $presences = $reduction->getPresence();

        return $this->render(
            'admin/reduction/show.html.twig',
            [
                'entity' => $reduction,
                'presences' => $presences,
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to delete a Reduction entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reduction_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']])
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Reduction entity.
     *
     * @Route("/{id}/edit", name="reduction_edit", methods={"GET","PUT"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function edit(Request $request, Reduction $reduction)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($reduction);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            $em->flush();

            $this->addFlash('success', 'La r??duction a bien ??t?? mis ?? jour');

            return $this->redirectToRoute('reduction');
        }

        return $this->render(
            'admin/reduction/edit.html.twig',
            [
                'entity' => $reduction,
                'edit_form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Creates a form to edit a Reduction entity.
     *
     * @param Reduction $entity The entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createEditForm(Reduction $entity)
    {
        $form = $this->createForm(
            ReductionType::class,
            $entity,
            [
                'action' => $this->generateUrl('reduction_edit', ['id' => $entity->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Update']);

        return $form;
    }

    /**
     * Deletes a Reduction entity.
     *
     * @Route("/{id}", name="reduction_delete", methods={"DELETE"})
     * @IsGranted("ROLE_MERCREDI_ADMIN")
     */
    public function delete(Request $request, Reduction $reduction)
    {
        $form = $this->createDeleteForm($reduction->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($reduction);
            $em->flush();

            $this->addFlash('success', 'La r??duction a bien ??t?? supprim??e');
        }

        return $this->redirectToRoute('reduction');
    }
}
