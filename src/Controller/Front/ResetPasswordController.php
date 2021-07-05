<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\ResetPassword\Form\ChangePasswordFormType;
use AcMarche\Mercredi\ResetPassword\Form\ResetPasswordRequestFormType;
use AcMarche\Mercredi\ResetPassword\Mailer\ResetPasswordMailer;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/reset-password")
 */
final class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private const MERCREDI_FRONT_FORGOT_PASSWORD_REQUEST = 'mercredi_front_forgot_password_request';
    private ResetPasswordHelperInterface $resetPasswordHelper;
    private UserPasswordHasherInterface $userPasswordEncoder;
    private ResetPasswordMailer $resetPasswordMailer;
    private UserRepository $userRepository;

    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        UserPasswordHasherInterface $userPasswordEncoder,
        ResetPasswordMailer $resetPasswordMailer,
        UserRepository $userRepository
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->resetPasswordMailer = $resetPasswordMailer;
        $this->userRepository = $userRepository;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="mercredi_front_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render(
            '@AcMarcheMercredi/front/reset_password/request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="mercredi_front_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (! $this->canCheckEmail()) {
            return $this->redirectToRoute(self::MERCREDI_FRONT_FORGOT_PASSWORD_REQUEST);
        }

        return $this->render(
            '@AcMarcheMercredi/front/reset_password/check_email.html.twig',
            [
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]
        );
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="mercredi_front_reset_password")
     */
    public function reset(
        Request $request,
        ?string $token = null
    ): Response {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('mercredi_front_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                'reset_password_error',
                sprintf(
                    'There was a problem validating your reset request - %s',
                    $e->getReason()
                )
            );

            return $this->redirectToRoute(self::MERCREDI_FRONT_FORGOT_PASSWORD_REQUEST);
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $this->userPasswordEncoder->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('mercredi_front_home');
        }

        return $this->render(
            '@AcMarcheMercredi/front/reset_password/reset.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function processSendingPasswordResetEmail(string $emailFormData): Response
    {
        $user = $this->userRepository->findOneBy(
            [
                'email' => $emailFormData,
            ]
        );

        // Marks that you are allowed to see the app_check_email page.
       // $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if ($user === null) {
            return $this->redirectToRoute('mercredi_front_check_email');
        }

        try {
            $resetPasswordToken = $this->resetPasswordHelper->generateResetToken($user);
            $this->setTokenObjectInSession($resetPasswordToken);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash(
                'reset_password_error',
                sprintf(
                    'There was a problem handling your password reset request - %s',
                    $e->getReason()
                )
            );

            return $this->redirectToRoute(self::MERCREDI_FRONT_FORGOT_PASSWORD_REQUEST);
        }

        $this->resetPasswordMailer->sendLink($user, $resetPasswordToken, $this->resetPasswordHelper->getTokenLifetime());

        return $this->redirectToRoute('mercredi_front_check_email');
    }
}
