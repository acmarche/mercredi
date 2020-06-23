<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Registration\EmailVerifier;
use AcMarche\Mercredi\Registration\Form\RegistrationFormType;
use AcMarche\Mercredi\Registration\Handler\RegistrationHandler;
use AcMarche\Mercredi\Registration\Mailer\RegistrationMailerFactory;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RegistrationMailerFactory
     */
    private $registrationMailerFactory;
    /**
     * @var RegistrationHandler
     */
    private $registrationHandler;

    public function __construct(
        EmailVerifier $emailVerifier,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        RegistrationMailerFactory $registrationMailerFactory,
        RegistrationHandler $registrationHandler
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->registrationMailerFactory = $registrationMailerFactory;
        $this->registrationHandler = $registrationHandler;
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
                $this->passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUsername($user->getEmail());

            $this->userRepository->persist($user);
            $this->userRepository->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                $this->registrationMailerFactory->generateMessagToVerifyEmail($user)
            );

            $this->addFlash('success', 'Votre compte a bien été créé, consultez votre boite mail');

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
    public function verifyUserEmail(Request $request): Response
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
