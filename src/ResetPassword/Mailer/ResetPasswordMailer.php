<?php

namespace AcMarche\Mercredi\ResetPassword\Mailer;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class ResetPasswordMailer
{
    use InitMailerTrait;

    public function sendLink(User $user, ResetPasswordToken $resetPasswordToken, int $tokenLifeTime): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Votre demande de changement de mot de passe')
            ->htmlTemplate('@AcMarcheMercredi/front/reset_password/email.html.twig')
            ->context(
                [
                    'resetToken' => $resetPasswordToken,
                    'tokenLifetime' => $tokenLifeTime,
                ]
            );

        $this->sendMail($templatedEmail);
    }
}
