<?php

namespace AcMarche\Mercredi\Contact\Mailer;

use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

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
    public function sendContactForm(string $from, string $nom, string $body): void
    {
        $to = $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $message = (new TemplatedEmail())
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
            ->from($from)
            ->to($to)
            ->text($body);

        $this->mailer->send($message);
    }
}
