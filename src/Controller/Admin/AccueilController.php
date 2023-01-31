<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Form\AccueilRetardAdminTpe;
use AcMarche\Mercredi\Accueil\Form\AccueilType;
use AcMarche\Mercredi\Accueil\Form\SearchAccueilByDate;
use AcMarche\Mercredi\Accueil\Handler\AccueilHandler;
use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Message\AccueilDeleted;
use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Accueil\Utils\AccueilUtils;
use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/accueil')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AccueilController extends AbstractController
{
    public function __construct(
        private AccueilRepository $accueilRepository,
        private AccueilHandler $accueilHandler,
        private RelationRepository $relationRepository,
        private AccueilCalculatorInterface $accueilCalculator,
        private FactureHandlerInterface $factureHandler,
        private FacturePresenceRepository $facturePresenceRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/index', name: 'mercredi_admin_accueil_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $accueils = [];
        $grouped = false;
        $date = null;
        $count = 0;
        $heure = '';
        $form = $this->createForm(SearchAccueilByDate::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $date = $data['date_jour'];
            $heure = $data['heure'];
            $ecole = $data['ecole'];
            $grouped = $data['groupEcole'];
            $accueils = $this->accueilRepository->findByDateHeureAndEcole($date, $heure, $ecole);
            $count = count($accueils);

            if ($grouped) {
                $accueils = AccueilUtils::groupByEcole($accueils);
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
                'grouped' => $grouped,
                'dateSelected' => $date,
                'count' => $count,
                'heure' => $heure,
            ]
        );
    }

    #[Route(path: '/list/{id}', name: 'mercredi_admin_accueil_show_enfant', methods: ['GET', 'POST'])]
    public function enfant(Enfant $enfant): Response
    {
        $accueils = $this->accueilRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/enfant.html.twig',
            [
                'accueils' => $accueils,
                'relations' => $relations,
                'enfant' => $enfant,
            ]
        );
    }

    #[Route(path: '/new/{tuteur}/{enfant}', name: 'mercredi_admin_accueil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $accueil = new Accueil($tuteur, $enfant);
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->accueilHandler->handleNew($enfant, $accueil);
            $this->dispatcher->dispatch(new AccueilCreated($result->getId()));

            return $this->redirectToRoute('mercredi_admin_accueil_show', [
                'id' => $result->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/show/{id}', name: 'mercredi_admin_accueil_show', methods: ['GET'])]
    public function show(Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();
        $cout = $this->accueilCalculator->calculate($accueil);
        $coutRetard = $this->accueilCalculator->calculateRetard($accueil);
        $facturePresence = $this->facturePresenceRepository->findByAccueil($accueil);

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'coutRetard' => $coutRetard,
                'enfant' => $enfant,
                'facturePresence' => $facturePresence,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_accueil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Accueil $accueil): Response
    {
        if ($this->factureHandler->isBilled($accueil->getId(), FactureInterface::OBJECT_ACCUEIL)) {
            $this->addFlash('danger', 'Un accueil déjà facturé ne peut être modifié');

            return $this->redirectToRoute('mercredi_admin_accueil_show', [
                'id' => $accueil->getId(),
            ]);
        }
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accueilRepository->flush();

            $this->dispatcher->dispatch(new AccueilUpdated($accueil->getId()));

            return $this->redirectToRoute('mercredi_admin_accueil_show', [
                'id' => $accueil->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/edit.html.twig',
            [
                'accueil' => $accueil,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/delete/{id}', name: 'mercredi_admin_accueil_delete', methods: ['POST'])]
    public function delete(Request $request, Accueil $accueil): RedirectResponse
    {
        $enfant = null;
        if ($this->isCsrfTokenValid('delete'.$accueil->getId(), $request->request->get('_token'))) {
            if ($this->factureHandler->isBilled($accueil->getId(), FactureInterface::OBJECT_ACCUEIL)) {
                $this->addFlash('danger', 'Un accueil déjà facturé ne peut être supprimé');

                return $this->redirectToRoute('mercredi_admin_accueil_show', [
                    'id' => $accueil->getId(),
                ]);
            }

            $id = $accueil->getId();
            $enfant = $accueil->getEnfant();
            $this->accueilRepository->remove($accueil);
            $this->accueilRepository->flush();
            $this->dispatcher->dispatch(new AccueilDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_enfant_show', [
            'id' => $enfant->getId(),
        ]);
    }

    #[Route(path: '/{id}/retard', name: 'mercredi_admin_accueil_retard', methods: ['GET', 'POST'])]
    #[IsGranted('accueil_show', subject: 'accueil')]
    public function retard(Request $request, Accueil $accueil): Response
    {
        $args = [
            'heure_retard' => $accueil->getHeureRetard(),
        ];
        $enfant = $accueil->getEnfant();

        $form = $this->createForm(AccueilRetardAdminTpe::class, $args);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $heureRetard = $data['heure_retard'];
            $delete = $data['delete'];
            if ($delete) {
                $accueil->setHeureRetard(null);
                $this->addFlash('success', 'Le retard a bien été supprimé');
            } else {
                $dateRetard = $accueil->getDateJour();
                $dateRetard->setTime($heureRetard->format('H'), $heureRetard->format('i'));
                $accueil->setHeureRetard($dateRetard);
                $this->addFlash('success', 'Le retard a bien été ajouté');
            }
            $this->accueilRepository->flush();

            return $this->redirectToRoute('mercredi_admin_accueil_show', [
                'id' => $accueil->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/accueil/retard.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
