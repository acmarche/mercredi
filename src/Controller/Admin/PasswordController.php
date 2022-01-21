<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Form\UserPasswordType;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/utilisateur/password')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PasswordController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     */
    #[Route(path: '/{id}/password', name: 'mercredi_admin_user_password', methods: ['GET', 'POST'])]
    public function passord(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('mercredi_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/user/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
