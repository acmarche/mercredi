<?php

namespace AcMarche\Mercredi\User\Mailer;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

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
     * @param User $user
     * @param Tuteur $tuteur
     * @param string|null $password
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendNewAccountToParent(User $user, Tuteur $tuteur, string $password = null)
    {
        $from = $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $message = (new TemplatedEmail())
            ->subject('Votre compte pour le site du mercredi')
            ->from($from)
            ->to($user->getEmail())
            ->textTemplate('@AcMarcheMercrediAdmin/account/_new_account_parent.txt.twig')
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
