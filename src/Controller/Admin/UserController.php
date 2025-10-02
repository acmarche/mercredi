<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\Checker\UserChecker;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\User\Form\UserEditType;
use AcMarche\Mercredi\User\Form\UserRoleType;
use AcMarche\Mercredi\User\Form\UserSearchType;
use AcMarche\Mercredi\User\Form\UserType;
use AcMarche\Mercredi\User\Message\UserCreated;
use AcMarche\Mercredi\User\Message\UserDeleted;
use AcMarche\Mercredi\User\Message\UserUpdated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/user')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordEncoder,
        private MessageBusInterface $dispatcher,
        private readonly TokenManager $tokenManager
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $users = [];
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $role = $data['role'];

            $users = $this->userRepository->findByNameOrRoles($nom, $role);
        }
        $bad = [];
        $users = $this->userRepository->findAllOrderByNom();
        foreach ($users as $user) {
            $check = UserChecker::check($user);
            if (count($check) > 0) {
                $bad[] = $check;
            }
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/index.html.twig',
            [
                'users' => $users,
                'bad' => $bad,
                'roles' => MercrediSecurityRole::explanation(),
                'form' => $form,
                'search' => $form->isSubmitted(),
            ],
        );
    }

    #[Route(path: '/new', name: 'mercredi_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $form->get('plainPassword')->getData()),
            );
            $user->setUsername($user->getEmail());
            $this->userRepository->persist($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserCreated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/new.html.twig',
            [
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/new/tuteur/{id}', name: 'mercredi_admin_user_new_from_tuteur', methods: ['GET', 'POST'])]
    public function newFromTuteur(Request $request, Tuteur $tuteur): Response
    {
        $user = User::newFromObject($tuteur);
        $user->addRole(MercrediSecurityRole::ROLE_PARENT);
        $user->addTuteur($tuteur);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $form->get('plainPassword')->getData()),
            );
            $user->setUsername($user->getEmail());
            $this->userRepository->persist($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserCreated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/new.html.twig',
            [
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}', name: 'mercredi_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $check = UserChecker::check($user);
        $tokenUrl = $this->tokenManager->getLinkToConnect($user);

        return $this->render(
            '@AcMarcheMercrediAdmin/user/show.html.twig',
            [
                'user' => $user,
                'check' => $check,
                'tokenUrl' => $tokenUrl,
            ],
        );
    }

    #[Route(path: '/{id}/edit', name: 'mercredi_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $editForm,
            ],
        );
    }

    #[Route(path: '/{id}/roles', name: 'mercredi_admin_user_roles', methods: ['GET', 'POST'])]
    public function roles(Request $request, User $user): Response
    {
        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/roles_edit.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/{id}/delete', name: 'mercredi_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $id = $user->getId();
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_user_index');
    }
}
