<?php

namespace AcMarche\Mercredi\Controller\Front;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Form\UserEditType;
use AcMarche\Mercredi\User\Form\UserPasswordType;
use AcMarche\Mercredi\User\Message\UserUpdated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/profile")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class ProfileController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @Route("/show", name="mercredi_front_user_show")
     *
     */
    public function show(): Response
    {
        /** @var User */
        $user = $this->getUser();
        if ($user == null) {
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

    /**
     * @Route("/redirect", name="mercredi_front_profile_redirect")
     */
    public function redirectByProfile(): Response
    {
        /** @var User */
        $user = $this->getUser();

        if ($user != null) {
            $roles = $user->getRoles();
            $del_val = 'ROLE_USER';

            $roles = array_filter(
                $roles,
                fn($e) => $e !== $del_val
            );

            if (\count($roles) > 1) {
                return $this->redirectToRoute('mercredi_front_select_profile');
            }

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

        return $this->redirectToRoute('mercredi_front_home');
    }

    /**
     * @Route("/select", name="mercredi_front_select_profile")
     * @IsGranted("ROLE_MERCREDI")
     */
    public function selectProfile(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/front/user/select_profile.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/edit", name="mercredi_front_user_edit")
     * @IsGranted("ROLE_MERCREDI")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            $this->dispatchMessage(new UserUpdated($user->getId()));

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

    /**
     * @Route("/password", name="mercredi_front_user_password")
     * @IsGranted("ROLE_MERCREDI")
     */
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
