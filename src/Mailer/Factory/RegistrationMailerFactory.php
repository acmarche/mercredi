<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

final class RegistrationMailerFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function generateMessagRegisgerSuccess(
        User $user,
        VerifyEmailSignatureComponents $verifyEmailSignatureComponents
    ): NotificationEmail {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->subject('Votre inscription Accueil Temps Libre')
            ->htmlTemplate('@AcMarcheMercrediEmail/front/registration/_mail_register_success.html.twig')
            ->context([
                'footer_text' => 'orga',
                'signedUrl' => $verifyEmailSignatureComponents->getSignedUrl(),
                'expiresAt' => $verifyEmailSignatureComponents->getExpiresAt(),
                'organisation' => $this->organisation,
            ]);

        return $message;
    }

    public function generateMessageToAdminAccountCreated(User $user): NotificationEmail
    {
        $email = $this->getEmailAddressOrganisation();
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->to($email)
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->subject('Un nouveau compte a été crée sur Accueil Temps Libre')
            ->htmlTemplate('@AcMarcheMercrediEmail/front/registration/_mail_new_account_created.html.twig')
            ->context(
                [
                    'user' => $user,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageSendLinkLostPassword(User $user, ResetPasswordToken $resetPasswordToken): NotificationEmail
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Votre demande de changement de mot de passe')
            ->htmlTemplate('@AcMarcheMercrediEmail/front/request_password.html.twig')
            ->context(
                [
                    'resetToken' => $resetPasswordToken,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }
}
