<?php

namespace AcMarche\Mercredi\Registration;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class EmailVerifier
{
    use InitMailerTrait;

    /**
     * @var \SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface
     */
    private $verifyEmailHelper;
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->userRepository = $userRepository;
    }

    public function sendEmailConfirmation(
        string $verifyEmailRouteName,
        UserInterface $user,
        TemplatedEmail $templatedEmail
    ): void {
        $verifyEmailSignatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getEmail()
        );

        $context = $templatedEmail->getContext();
        $context['signedUrl'] = $verifyEmailSignatureComponents->getSignedUrl();
        $context['expiresAt'] = $verifyEmailSignatureComponents->getExpiresAt();

        $templatedEmail->context($context);

        $this->sendMail($templatedEmail);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->userRepository->persist($user);
        $this->userRepository->flush();
    }
}
