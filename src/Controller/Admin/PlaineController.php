<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Plaine\Dto\PlaineGroupingByDayDto;
use AcMarche\Mercredi\Plaine\Form\PlaineOpenType;
use AcMarche\Mercredi\Plaine\Form\PlaineType;
use AcMarche\Mercredi\Plaine\Form\SearchPlaineType;
use AcMarche\Mercredi\Plaine\Handler\PlaineAdminHandler;
use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use AcMarche\Mercredi\Plaine\Message\PlaineDeleted;
use AcMarche\Mercredi\Plaine\Message\PlaineUpdated;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plaine')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PlaineController extends AbstractController
{
    public function __construct(
        private PlaineRepository $plaineRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private GroupeScolaireRepository $groupeScolaireRepository,
        private PlaineAdminHandler $plaineAdminHandler,
        private GroupingInterface $grouping,
        private MessageBusInterface $dispatcher,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_plaine_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $nom = null;
        $archived = false;
        $form = $this->createForm(SearchPlaineType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $archived = $data['archived'];
            $nom = $data['nom'];
        }
        $plaines = $this->plaineRepository->search($nom, $archived);
        array_map(function ($plaine) {
            $plaine->enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
        }, $plaines);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/index.html.twig',
            [
                'plaines' => $plaines,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_plaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $plaine = new Plaine();
        foreach ($this->groupeScolaireRepository->findAllOrderByNom() as $groupeScolaire) {
            if ($groupeScolaire->getNom() == 'Premats') {
                continue;
            }
            $plaineGroupe = new PlaineGroupe($plaine, $groupeScolaire);
            $plaine->addPlaineGroupe($plaineGroupe);
        }
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->persist($plaine);
            $this->plaineRepository->flush();

            $this->dispatcher->dispatch(new PlaineCreated($plaine->getId()));

            return $this->redirectToRoute('mercredi_admin_plaine_jour_edit', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/new.html.twig',
            [
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_plaine_show', methods: ['GET'])]
    public function show(Plaine $plaine): Response
    {
        if (\count($plaine->getJours()) < 1) {
            $this->addFlash('danger', 'La plaine doit contenir des dates');

            return $this->redirectToRoute('mercredi_admin_plaine_jour_edit', [
                'id' => $plaine->getId(),
            ]);
        }
        /**
         * @var PlaineGroupingByDayDto[] $data
         */
        $data = [];
        foreach ($plaine->getJours() as $jour) {
            $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndJour($plaine, $jour);
            $groupes = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);
            $plaineGroupingDto = new PlaineGroupingByDayDto($jour, $enfants, $groupes);
            $data[$jour->getId()] = $plaineGroupingDto;
        }

        $enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
        $this->grouping->setEnfantsForGroupesScolaire($plaine, $enfants);

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
                'data' => $data,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_plaine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->flush();

            $this->dispatcher->dispatch(new PlaineUpdated($plaine->getId()));

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/open', name: 'mercredi_admin_plaine_open', methods: ['GET', 'POST'])]
    public function open(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineOpenType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (($plaineOpen = $this->plaineAdminHandler->handleOpeningRegistrations($plaine)) === null) {
                $this->plaineRepository->flush();
                $this->dispatcher->dispatch(new PlaineUpdated($plaine->getId()));
            } else {
                $this->addFlash(
                    'danger',
                    'Les inscriptions n\'ont pas pu être ouvrir car la plaine '.$plaineOpen->getNom(
                    ).' est toujours ouverte',
                );
            }

            return $this->redirectToRoute('mercredi_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/plaine/open.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_plaine_delete', methods: ['POST'])]
    public function delete(Request $request, Plaine $plaine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$plaine->getId(), $request->request->get('_token'))) {
            $plaineId = $plaine->getId();
            $this->plaineRepository->remove($plaine);
            $this->plaineRepository->flush();
            $this->dispatcher->dispatch(new PlaineDeleted($plaineId));
        }

        return $this->redirectToRoute('mercredi_admin_plaine_index');
    }
}
