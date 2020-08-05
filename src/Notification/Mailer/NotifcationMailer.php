<?php

namespace AcMarche\Mercredi\Notification\Mailer;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class NotifcationMailer
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
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(MailerInterface $mailer, OrganisationRepository $organisationRepository)
    {
        $this->mailer = $mailer;
        $this->organisationRepository = $organisationRepository;
        $this->organisation = $organisationRepository->getOrganisation();
    }

    public function sendMessagEnfantCreated(User $user, Enfant $enfant): void
    {
        $email = (new TemplatedEmail())
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

        $this->mailer->send($email);
    }
}
