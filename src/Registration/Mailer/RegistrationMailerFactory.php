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

    public function generateMessagRegisgerSuccess(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Inscription Accueil Temps Libre')
            ->htmlTemplate('@AcMarcheMercredi/front/registration/_mail_register_success.html.twig');
    }

    public function generateMessagToVerifyEmail(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Inscription, vérifiez votre email')
            ->htmlTemplate('@AcMarcheMercredi/front/registration/confirmation_email.html.twig');
    }

    public function generateMessageToAdminAccountCreated(User $user): TemplatedEmail
    {
        $email = $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        return (new TemplatedEmail())
            ->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($email)
            ->subject('Un nouveau compte a été crée sur Accueil Temps Libre')
            ->textTemplate('@AcMarcheMercredi/front/registration/_mail_new_account_created.txt.twig')
            ->context(['user' => $user]);
    }
}
