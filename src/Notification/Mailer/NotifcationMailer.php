<?php

namespace AcMarche\Mercredi\Notification\Mailer;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class NotifcationMailer
{
    use InitMailerTrait;

    public function sendMessagEnfantCreated(User $user, Enfant $enfant): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($user->getEmail())
            ->to($this->organisation->getEmail())
            ->subject('Un enfant a été ajouté par '.$user->getNom().' '.$user->getPrenom())
            ->textTemplate('@AcMarcheMercredi/parent/enfant/_mail_add_enfant.txt.twig')
            ->context(
                [
                    'user' => $user,
                    'enfant' => $enfant,
                ]
            );

        $this->sendMail($templatedEmail);
    }
}
