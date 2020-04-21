<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\User;
use AcMarche\Mercredi\Utilisateur\Form\UtilisateurPasswordType;
use AcMarche\Mercredi\Repository\UserRepository;
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
     * @Route("/{id}/password", name="admin_mercredi_utilisateur_password", methods={"GET","POST"})
     *
     */
    public function passord(Request $request, User $utilisateur)
    {
        $form = $this->createForm(UtilisateurPasswordType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->encodePassword($utilisateur, $form->getData()->getPlainPassword());
            $utilisateur->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash("success", "Le mot de passe a bien été modifié");

            return $this->redirectToRoute('admin_mercredi_utilisateur_show', ['id' => $utilisateur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/utilisateur/password.html.twig',
            array(
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            )
        );
    }


}
