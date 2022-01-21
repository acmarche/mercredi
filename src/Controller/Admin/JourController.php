<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Contrat\Tarification\TarificationFormGeneratorInterface;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Form\JourType;
use AcMarche\Mercredi\Jour\Form\SearchJourType;
use AcMarche\Mercredi\Jour\Message\JourCreated;
use AcMarche\Mercredi\Jour\Message\JourDeleted;
use AcMarche\Mercredi\Jour\Message\JourUpdated;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/jour')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class JourController extends AbstractController
{
    public function __construct(
        private JourRepository $jourRepository,
        private TarificationFormGeneratorInterface $tarificationFormGenerator,
        private PresenceRepository $presenceRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_jour_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchJourType::class);
        $form->handleRequest($request);
        $archived = false;
        $pedagogique = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $archived = $data['archived'];
            $pedagogique = $data['pedagogique'];
        }
        $jours = $this->jourRepository->search($archived, $pedagogique);

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/index.html.twig',
            [
                'jours' => $jours,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_jour_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $jour = new Jour();
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatcher->dispatch(new JourCreated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_tarif', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/new.html.twig',
            [
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/tarif/{id}', name: 'mercredi_admin_jour_tarif', methods: ['GET', 'POST'])]
    public function tarif(Request $request, Jour $jour): Response
    {
        $form = $this->tarificationFormGenerator->generateForm($jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatcher->dispatch(new JourCreated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/tarif.html.twig',
            [
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_jour_show', methods: ['GET'])]
    public function show(Jour $jour): Response
    {
        $tarifs = $this->tarificationFormGenerator->generateTarifsHtml($jour);
        $presences = $this->presenceRepository->findByDay($jour);

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/show.html.twig',
            [
                'jour' => $jour,
                'tarifs' => $tarifs,
                'presences' => $presences,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_jour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Jour $jour): Response
    {
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->flush();
            //todo switch pedagogique

            $this->dispatcher->dispatch(new JourUpdated($jour->getId()));

            return $this->redirectToRoute('mercredi_admin_jour_show', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/jour/edit.html.twig',
            [
                'jour' => $jour,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_jour_delete', methods: ['POST'])]
    public function delete(Request $request, Jour $jour): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$jour->getId(), $request->request->get('_token'))) {
            $jourId = $jour->getId();
            $this->jourRepository->remove($jour);
            $this->jourRepository->flush();
            $this->dispatcher->dispatch(new JourDeleted($jourId));
        }

        return $this->redirectToRoute('mercredi_admin_jour_index');
    }
}
