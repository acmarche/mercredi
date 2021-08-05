<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AdminEmailFactory
{
    use OrganisationPropertyInitTrait;

    public function sendMessagEnfantCreated(User $user, Enfant $enfant): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $message
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

        return $message;
    }

    /**
     * @param array|Enfant[] $enfants
     * @throws TransportExceptionInterface
     */
    public function sendMessagEnfantsOrphelins(array $enfants): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $message
            ->from($email)
            ->to($email)
            ->subject('Des enfants orphelins ont été trouvés')
            ->textTemplate('@AcMarcheMercredi/front/mail/_mail_orphelins.html.twig')
            ->context(
                [
                    'enfants' => $enfants,
                ]
            );

        return $message;
    }

    /**
     * @param array|Tuteur[] $tuteurs
     * @throws TransportExceptionInterface
     */
    public function sendMessagTuteurArchived(array $tuteurs): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $message
            ->from($email)
            ->to($email)
            ->subject('Les tuteurs ont été archivés')
            ->textTemplate('@AcMarcheMercredi/front/mail/_mail_tuteurs_archived.html.twig')
            ->context(
                [
                    'tuteurs' => $tuteurs,
                ]
            );

        return $message;
    }
}
