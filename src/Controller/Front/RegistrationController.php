<?php

namespace AcMarche\Mercredi\Controller\Front;

use Symfony\Component\HttpFoundation\RedirectResponse;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Registration\EmailVerifier;
use AcMarche\Mercredi\Registration\Form\RegistrationFormType;
use AcMarche\Mercredi\Registration\Message\RegisterCreated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private UserRepository $userRepository;

    public function __construct(
        EmailVerifier $emailVerifier,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepository $userRepository
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/register", name="mercredi_front_register")
     */
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUsername($user->getEmail());
            $user->setEnabled(true);

            $this->userRepository->persist($user);
            $this->userRepository->flush();

            $this->dispatchMessage(new RegisterCreated($user->getId()));

            return $this->redirectToRoute('mercredi_front_home');
        }

        return $this->render(
            '@AcMarcheMercredi/front/registration/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('mercredi_front_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('mercredi_front_register');
    }
}
