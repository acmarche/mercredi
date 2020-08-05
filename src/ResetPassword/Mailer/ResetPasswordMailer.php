<?php

namespace AcMarche\Mercredi\ResetPassword\Mailer;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class ResetPasswordMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(MailerInterface $mailer, OrganisationRepository $organisationRepository)
    {
        $this->mailer = $mailer;
        $this->organisation = $organisationRepository->getOrganisation();
    }

    public function sendLink(User $user, ResetPasswordToken $resetToken, int $tokenLifeTime): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Votre demande de changement de mot de passe')
            ->htmlTemplate('@AcMarcheMercredi/front/reset_password/email.html.twig')
            ->context(
                [
                    'resetToken' => $resetToken,
                    'tokenLifetime' => $tokenLifeTime,
                ]
            );

        $this->mailer->send($email);
    }
}
