<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Form\ValidateForm;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Form\AddChildAutocompleteType;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Form\SearchTuteurType;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use AcMarche\Mercredi\User\Handler\AssociationTuteurHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/tuteur')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class TuteurController extends AbstractController
{
    public function __construct(
        private TuteurRepository $tuteurRepository,
        private FactureRepository $factureRepository,
        private RelationRepository $relationRepository,
        private AccueilRepository $accueilRepository,
        private PresenceRepository $presenceRepository,
        private SearchHelper $searchHelper,
        private MessageBusInterface $dispatcher,
        private AssociationTuteurHandler $associationTuteurHandler,
    ) {}

    #[Route(path: '/', name: 'mercredi_admin_tuteur_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);

        $tuteurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $tuteurs = $this->tuteurRepository->search($data['nom'], $data['archived']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/index.html.twig',
            [
                'tuteurs' => $tuteurs,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_tuteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurCreated($tuteur->getId()));

            if ($tuteur->createAccount) {
                $user = $this->associationTuteurHandler->handleCreateUserFromTuteur($tuteur);
                $password = $user->getPlainPassword();
                $this->addFlash('success', 'Un compte a été créé pour le parent');
                $accountInfo = $this->renderView('@AcMarcheMercrediAdmin/quick/_account_info.txt.twig', [
                    'user' => $user,
                    'password' => $password,
                ]);
                $this->addFlash('info', $accountInfo);
            }

            return $this->redirectToRoute('mercredi_admin_tuteur_show', [
                'id' => $tuteur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/new/fromuser/{id}', name: 'mercredi_admin_tuteur_new_from_user', methods: ['GET', 'POST'])]
    public function newFromUser(Request $request, User $user): Response
    {
        $tuteur = new Tuteur();
        $tuteur->setNom($user->getNom());
        $tuteur->setPrenom($user->getPrenom());
        $tuteur->setEmail($user->getEmail());
        $tuteur->setGsm($user->getTelephone());

        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();

            $associateUserTuteurDto = new AssociateUserTuteurDto($user);
            $associateUserTuteurDto->tuteur = $tuteur;
            $associateUserTuteurDto->sendEmail = true;
            $this->associationTuteurHandler->handleAssociateTuteur($associateUserTuteurDto);

            $this->dispatcher->dispatch(new TuteurCreated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', [
                'id' => $tuteur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_tuteur_show', methods: ['GET'])]
    public function show(Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);
        $form = $this->createForm(AddChildAutocompleteType::class, null, [
            'action' => $this->generateUrl('mercredi_admin_relation_attach_enfant', ['id' => $tuteur->getId()]),
        ]);

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
                'relations' => $relations,
                'formAddChild' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_tuteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', [
                'id' => $tuteur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_tuteur_delete', methods: ['POST'])]
    public function delete(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            $presences = $this->presenceRepository->findByTuteur($tuteur);
            $accueils = $this->accueilRepository->findByTuteur($tuteur);
            $factures = $this->factureRepository->findByTuteur($tuteur);

            $form = $this->createForm(ValidateForm::class, null, [
                'action' =>
                    $this->generateUrl('mercredi_admin_tuteur_delete_confirmed', ['id' => $tuteur->getId()]),
                'method' => 'POST',
            ]);


            return $this->render(
                '@AcMarcheMercrediAdmin/tuteur/delete.html.twig',
                [
                    'tuteur' => $tuteur,
                    'accueils' => $accueils,
                    'presences' => $presences,
                    'factures' => $factures,
                    'form' => $form->createView(),
                ],
            );
        }

        $this->addFlash('warning', 'Page non accessible');

        return $this->redirectToRoute('mercredi_admin_tuteur_index');
    }

    #[Route(path: '/{id}/delete/confirmed', name: 'mercredi_admin_tuteur_delete_confirmed', methods: ['POST'])]
    public function deleteConfirmed(Request $request, Tuteur $tuteur): RedirectResponse
    {
        $form = $this->createForm(ValidateForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($this->factureRepository->findByTuteur($tuteur) as $facture) {
                $this->factureRepository->remove($facture);
            }
            $id = $tuteur->getId();
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatcher->dispatch(new TuteurDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_index');
    }
}
