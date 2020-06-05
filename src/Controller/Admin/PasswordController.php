<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\User;
use AcMarche\Mercredi\User\Form\UserPasswordType;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/utilisateur/password")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class PasswordController extends AbstractController
{
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     *
     * @Route("/{id}/password", name="mercredi_admin_utilisateur_password", methods={"GET","POST"})
     */
    public function passord(Request $request, User $utilisateur)
    {
        $form = $this->createForm(UserPasswordType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($utilisateur, $form->getData()->getPlainPassword());
            $utilisateur->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('mercredi_admin_utilisateur_show', ['id' => $utilisateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/password.html.twig',
            [
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }
}
