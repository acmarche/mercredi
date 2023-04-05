<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Form\UserEditType;
use AcMarche\Mercredi\User\Form\UserPasswordType;
use AcMarche\Mercredi\User\Message\UserUpdated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/profile')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordEncoder,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/show', name: 'mercredi_front_user_show')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user) {
            $this->addFlash('warning', 'Votre compte n\'est pas encore actif');

            return $this->redirectToRoute('mercredi_front_home');
        }

        return $this->render(
            '@AcMarcheMercredi/front/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(path: '/redirect', name: 'mercredi_front_profile_redirect')]
    public function redirectByProfile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null !== $user) {
            $roles = $user->getRoles();
            $del_val = 'ROLE_USER';

            $roles = array_filter(
                $roles,
                fn($e) => $e !== $del_val
            );

            if (\count($roles) > 1) {
                return $this->redirectToRoute('mercredi_front_select_profile');
            }
dd($roles);
            if ($user->hasRole('ROLE_MERCREDI_PARENT')) {
                return $this->redirectToRoute('mercredi_parent_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ECOLE')) {
                return $this->redirectToRoute('mercredi_ecole_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ANIMATEUR')) {
                return $this->redirectToRoute('mercredi_animateur_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ADMIN') || $user->hasRole('ROLE_MERCREDI_READ')) {
                return $this->redirectToRoute('mercredi_admin_home');
            }
        }
        $this->addFlash('warning', 'Aucun rôle ne vous a été attribué');
return $this->render(
            '@AcMarcheMercredi/front/user/select_profile.html.twig',
            [
            ]
        );
        return $this->redirectToRoute('mercredi_front_home');
    }

    #[Route(path: '/select', name: 'mercredi_front_select_profile')]

    public function selectProfile(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/front/user/select_profile.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/edit', name: 'mercredi_front_user_edit')]
    #[IsGranted('ROLE_MERCREDI')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('mercredi_front_user_show');
        }

        return $this->render(
            '@AcMarcheMercredi/front/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/password', name: 'mercredi_front_user_password')]
    #[IsGranted('ROLE_MERCREDI')]
    public function password(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('mercredi_front_user_show');
        }

        return $this->render(
            '@AcMarcheMercredi/front/user/password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
