<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\TemplatedEmailFactory;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class UserEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function messageNewAccountToTuteur(
        User $user,
        Tuteur $tuteur,
        string $tokenUrl,
        ?string $password = null
    ): TemplatedEmail {
        $message = TemplatedEmailFactory::asPublicEmailJf();
        $message
            ->subject('Informations sur votre compte de '.$this->organisation->getNom())
            ->from($this->getEmailSenderAddress())
            ->to($user->getEmail())
            ->bcc($this->getEmailContact())
            ->htmlTemplate('@AcMarcheMercrediEmail/welcome/_mail_welcome_parent.html.twig')
            ->context(
                [
                    'tuteur' => $tuteur,
                    'tokenUrl' => $tokenUrl,
                    'user' => $user,
                    'password' => $password,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageNewAccountToAnimateur(
        User $user,
        Animateur $animateur,
        string $tokenUrl,
        ?string $password = null
    ): TemplatedEmail {

        $message = TemplatedEmailFactory::asPublicEmailJf();
        $message
            ->subject('Informations sur votre compte de '.$this->organisation->getNom())
            ->from(new Address($this->getEmailSenderAddress()))
            ->to($user->getEmail())
            ->bcc($this->getEmailContact())
            ->htmlTemplate('@AcMarcheMercrediEmail/welcome/_mail_welcome_animateur.html.twig')
            ->context(
                [
                    'animateur' => $animateur,
                    'user' => $user,
                    'tokenUrl' => $tokenUrl,
                    'password' => $password,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageSendAutoLogin(
        User $user,
        string $tokenUrl
    ): TemplatedEmail {
        $message = TemplatedEmailFactory::asPublicEmailJf();
        $message
            ->subject('Connection à votre compte pour '.$this->organisation->getNom())
            ->from($this->getEmailSenderAddress())
            ->to($user->getEmail())
            ->htmlTemplate('@AcMarcheMercrediEmail/welcome/_mail_auto_login.html.twig')
            ->context(
                [
                    'user' => $user,
                    'tokenUrl' => $tokenUrl,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

}
