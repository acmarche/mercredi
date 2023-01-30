<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Form\SearchTuteurType;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Handler\AssociationTuteurHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tuteur')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class TuteurController extends AbstractController
{
    public function __construct(
        private TuteurRepository $tuteurRepository,
        private RelationRepository $relationRepository,
        private SearchHelper $searchHelper,
        private MessageBusInterface $dispatcher,
        private AssociationTuteurHandler $associationTuteurHandler
    ) {
    }

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
            ]
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
            ]
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_tuteur_show', methods: ['GET'])]
    public function show(Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
                'relations' => $relations,
            ]
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
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_tuteur_delete', methods: ['POST'])]
    public function delete(Request $request, Tuteur $tuteur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            /*      if (count($this->presenceRepository->findByTuteur($tuteur)) > 0) {
                      $this->addFlash('danger', 'Ce tuteur ne peut pas être supprimé car il y a des présences à son nom');

                      return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
                  }*/

            $id = $tuteur->getId();
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatcher->dispatch(new TuteurDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_index');
    }
}
