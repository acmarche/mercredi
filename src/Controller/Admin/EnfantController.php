<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Enfant\Form\EnfantType;
use AcMarche\Mercredi\Enfant\Form\SearchEnfantType;
use AcMarche\Mercredi\Enfant\Handler\EnfantHandler;
use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Form\ValidateForm;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/enfant')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class EnfantController extends AbstractController
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private EnfantHandler $enfantHandler,
        private RelationRepository $relationRepository,
        private PresenceRepository $presenceRepository,
        private AccueilRepository $accueilRepository,
        private PresenceUtils $presenceUtils,
        private SearchHelper $searchHelper,
        private PlainePresenceRepository $plainePresenceRepository,
        private MessageBusInterface $dispatcher,
        private FactureCalculatorInterface $factureCalculator
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_enfant_index', methods: ['GET', 'POST'])]
    #[Route(path: '/all/{all}', name: 'mercredi_admin_enfant_all', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchEnfantType::class);
        $form->handleRequest($request);
        $enfants = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::ENFANT_LIST, $data);
            $enfants = $this->enfantRepository->search(
                $data['nom'],
                $data['ecole'],
                $data['annee_scolaire'],
                $data['archived']
            );
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        );
    }

    #[Route(path: '/new/{id}', name: 'mercredi_admin_enfant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $tuteur);
            $this->dispatcher->dispatch(new EnfantCreated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_enfant_show', methods: ['GET'])]
    public function show(Enfant $enfant): Response
    {
        $relations = $this->relationRepository->findByEnfant($enfant);
        $presences = $this->presenceRepository->findByEnfant($enfant);

        $presencesGrouped = $this->presenceUtils->groupByYear($presences);
        $fratries = $this->relationRepository->findFrateries($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);

        end($presencesGrouped);
        $key = key($presencesGrouped);
        $currentYear = $key;//if empty in current year

        foreach ($presences as $presence) {
            $presence->paid = $this->factureCalculator->isPresencePaid($presence);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'fratries' => $fratries,
                'relations' => $relations,
                'prensencesGrouped' => $presencesGrouped,
                'plaines' => $plaines,
                'currentYear' => $currentYear,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_enfant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatcher->dispatch(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('mercredi_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_enfant_delete', methods: ['POST'])]
    public function delete(Request $request, Enfant $enfant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enfant->getId(), $request->request->get('_token'))) {

            $presences = $this->presenceRepository->findByEnfant($enfant);
            $accueils = $this->accueilRepository->findByEnfant($enfant);

            $form = $this->createForm(ValidateForm::class, null, [
                'action' =>
                    $this->generateUrl('mercredi_admin_enfant_delete_confirmed', ['id' => $enfant->getId()]),
                'method' => 'POST',
            ]);

            return $this->render(
                '@AcMarcheMercrediAdmin/enfant/delete.html.twig',
                [
                    'enfant' => $enfant,
                    'accueils' => $accueils,
                    'presences' => $presences,
                    'form' => $form->createView(),
                ]
            );
        }

        return $this->redirectToRoute('mercredi_admin_enfant_index');
    }

    #[Route(path: '/{id}/delete/confirmed', name: 'mercredi_admin_enfant_delete_confirmed', methods: ['POST'])]
    public function deleteConfirmed(Request $request, Enfant $enfant): RedirectResponse
    {
        $form = $this->createForm(ValidateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enfantId = $enfant->getId();
            $this->enfantRepository->remove($enfant);
            $this->enfantRepository->flush();
            $this->dispatcher->dispatch(new EnfantDeleted($enfantId));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_index');
    }
}
