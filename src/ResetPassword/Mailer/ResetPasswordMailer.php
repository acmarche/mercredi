<?php

namespace AcMarche\Mercredi\ResetPassword\Mailer;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class ResetPasswordMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendLink(User $user, ResetPasswordToken $resetPasswordToken): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Votre demande de changement de mot de passe')
            ->htmlTemplate('@AcMarcheMercredi/front/reset_password/email.html.twig')
            ->context(
                [
                    'resetToken' => $resetPasswordToken,
                ]
            );

        $this->sendMail($templatedEmail);
    }
}
