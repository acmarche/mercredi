<?php

namespace AcMarche\Mercredi\Registration\Mailer;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class RegistrationMailerFactory
{
    /**
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisation = $organisationRepository->getOrganisation();
    }

    public function generateMessagToVerifyEmail(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Inscription, vérifiez votre email')
            ->htmlTemplate('@AcMarcheMercredi/front/registration/confirmation_email.html.twig');
    }
}
