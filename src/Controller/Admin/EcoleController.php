<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Ecole\Form\EcoleType;
use AcMarche\Mercredi\Ecole\Message\EcoleCreated;
use AcMarche\Mercredi\Ecole\Message\EcoleDeleted;
use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/ecole')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class EcoleController extends AbstractController
{
    public function __construct(
        private EcoleRepository $ecoleRepository,
        private EnfantRepository $enfantRepository,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_ecole_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoleRepository->findAllOrderByNom(),
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_ecole_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ecole = new Ecole();
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->persist($ecole);
            $this->ecoleRepository->flush();

            $this->dispatcher->dispatch(new EcoleCreated($ecole->getId()));

            return $this->redirectToRoute('mercredi_admin_ecole_show', [
                'id' => $ecole->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/new.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_ecole_show', methods: ['GET'])]
    public function show(Ecole $ecole): Response
    {
        $enfants = $this->enfantRepository->findByEcoles([$ecole]);

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/show.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_ecole_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ecole $ecole): Response
    {
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->flush();

            $this->dispatcher->dispatch(new EcoleUpdated($ecole->getId()));

            return $this->redirectToRoute('mercredi_admin_ecole_show', [
                'id' => $ecole->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/ecole/edit.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_ecole_delete', methods: ['POST'])]
    public function delete(Request $request, Ecole $ecole): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$ecole->getId(), $request->request->get('_token'))) {
            if ([] !== $this->enfantRepository->findByEcoles([$ecole])) {
                $this->addFlash('danger', 'L\'école contient des enfants et ne peut être supprimée');

                return $this->redirectToRoute('mercredi_admin_ecole_show', [
                    'id' => $ecole->getId(),
                ]);
            }
            $ecoleId = $ecole->getId();
            $this->ecoleRepository->remove($ecole);
            $this->ecoleRepository->flush();
            $this->dispatcher->dispatch(new EcoleDeleted($ecoleId));
        }

        return $this->redirectToRoute('mercredi_admin_ecole_index');
    }
}
