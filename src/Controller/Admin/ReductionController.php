<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Reduction\Form\ReductionType;
use AcMarche\Mercredi\Reduction\Message\ReductionCreated;
use AcMarche\Mercredi\Reduction\Message\ReductionDeleted;
use AcMarche\Mercredi\Reduction\Message\ReductionUpdated;
use AcMarche\Mercredi\Reduction\Repository\ReductionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/reduction')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class ReductionController extends AbstractController
{
    public function __construct(
        private ReductionRepository $reductionRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_reduction_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/index.html.twig',
            [
                'reductions' => $this->reductionRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_reduction_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reductionRepository->persist($reduction);
            $this->reductionRepository->flush();

            $this->dispatcher->dispatch(new ReductionCreated($reduction->getId()));

            return $this->redirectToRoute('mercredi_admin_reduction_show', [
                'id' => $reduction->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/new.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_reduction_show', methods: ['GET'])]
    public function show(Reduction $reduction): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/show.html.twig',
            [
                'reduction' => $reduction,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_reduction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reduction $reduction): Response
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reductionRepository->flush();

            $this->dispatcher->dispatch(new ReductionUpdated($reduction->getId()));

            return $this->redirectToRoute('mercredi_admin_reduction_show', [
                'id' => $reduction->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/reduction/edit.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_reduction_delete', methods: ['POST'])]
    public function delete(Request $request, Reduction $reduction): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $id = $reduction->getId();
            $this->reductionRepository->remove($reduction);
            $this->reductionRepository->flush();
            $this->dispatcher->dispatch(new ReductionDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_reduction_index');
    }
}
