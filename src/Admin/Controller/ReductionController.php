<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Reduction\Message\ReductionCreated;
use AcMarche\Mercredi\Reduction\Message\ReductionDeleted;
use AcMarche\Mercredi\Reduction\Message\ReductionUpdated;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Reduction\Form\ReductionType;
use AcMarche\Mercredi\Reduction\Repository\ReductionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reduction")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class ReductionController extends AbstractController
{
    /**
     * @var ReductionRepository
     */
    private $reductionRepository;

    public function __construct(ReductionRepository $reductionRepository)
    {
        $this->reductionRepository = $reductionRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_reduction_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/index.html.twig',
            [
                'reductions' => $this->reductionRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_reduction_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->reductionRepository->persist($reduction);
            $this->reductionRepository->flush();

            $this->dispatchMessage(new ReductionCreated($reduction->getId()));

            return $this->redirectToRoute('mercredi_admin_reduction_show', ['id' => $reduction->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/new.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_reduction_show", methods={"GET"})
     */
    public function show(Reduction $reduction): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/show.html.twig',
            [
                'reduction' => $reduction,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_reduction_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reduction $reduction): Response
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reductionRepository->flush();

            $this->dispatchMessage(new ReductionUpdated($reduction->getId()));

            return $this->redirectToRoute('mercredi_admin_reduction_show', ['id' => $reduction->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/edit.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_reduction_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reduction $reduction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $this->reductionRepository->remove($reduction);
            $this->reductionRepository->flush();
            $this->dispatchMessage(new ReductionDeleted($reduction->getId()));
        }

        return $this->redirectToRoute('mercredi_admin_reduction_index');
    }
}
