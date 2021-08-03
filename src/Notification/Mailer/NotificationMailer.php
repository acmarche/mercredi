<?php

namespace AcMarche\Mercredi\Notification\Mailer;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class NotificationMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

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

    /**
     * @param array|Enfant[] $enfants
     * @throws TransportExceptionInterface
     */
    public function sendMessagEnfantsOrphelins(array $enfants): void
    {
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $templatedEmail = (new TemplatedEmail())
            ->from($email)
            ->to($email)
            ->subject('Des enfants orphelins ont été trouvés')
            ->textTemplate('@AcMarcheMercredi/front/mail/_mail_orphelins.html.twig')
            ->context(
                [
                    'enfants' => $enfants,
                ]
            );

        $this->sendMail($templatedEmail);
    }

    /**
     * @param array|Tuteur[] $tuteurs
     * @throws TransportExceptionInterface
     */
    public function sendMessagTuteurArchived(array $tuteurs): void
    {
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $templatedEmail = (new TemplatedEmail())
            ->from($email)
            ->to($email)
            ->subject('Les tuteurs ont été archivés')
            ->textTemplate('@AcMarcheMercredi/front/mail/_mail_tuteurs_archived.html.twig')
            ->context(
                [
                    'tuteurs' => $tuteurs,
                ]
            );

        $this->sendMail($templatedEmail);
    }
}
