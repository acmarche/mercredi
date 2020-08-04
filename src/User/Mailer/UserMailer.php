<?php

namespace AcMarche\Mercredi\User\Mailer;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UserMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var \AcMarche\Mercredi\Entity\Organisation|null
     */
    private $organisation;

    public function __construct(
        MailerInterface $mailer,
        OrganisationRepository $organisationRepository
    ) {
        $this->mailer = $mailer;
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNewAccountToParent(User $user, Tuteur $tuteur, string $password = null)
    {
        $from = $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $message = (new TemplatedEmail())
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->htmlTemplate('@AcMarcheMercredi/front/mail/_mail_welcome_parent.html.twig')
            ->context(
                [
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                    'organisation' => $this->organisation,
                ]
            );

        $this->mailer->send($message);
    }
}
