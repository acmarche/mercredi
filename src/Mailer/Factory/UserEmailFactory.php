<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class UserEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function messageNewAccountToTuteur(User $user, Tuteur $tuteur, ?string $password = null): NotificationEmail
    {
        $from = null !== $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->bcc($from)
            ->htmlTemplate('@AcMarcheMercrediEmail/welcome/_mail_welcome_parent.html.twig')
            ->context(
                [
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageNewAccountToAnimateur(
        User $user,
        Animateur $animateur,
        ?string $password = null
    ): NotificationEmail {
        $from = null !== $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->bcc($from)
            ->htmlTemplate('@AcMarcheMercrediEmail/welcome/_mail_welcome_animateur.html.twig')
            ->context(
                [
                    'animateur' => $animateur,
                    'user' => $user,
                    'password' => $password,
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }
}
