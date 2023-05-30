<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminEmailFactory
{
    use OrganisationPropertyInitTrait;

    public function messageEnfantCreated(User|UserInterface $user, Enfant $enfant): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->from($user->getEmail())
            ->to($this->organisation->getEmail())
            ->subject('Un enfant a été ajouté par '.$user->getNom().' '.$user->getPrenom())
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_add_enfant.html.twig')
            ->context(
                [
                    'user' => $user,
                    'enfant' => $enfant,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function messagEnfantsOrphelins(array $enfants): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject('Des enfants orphelins ont été trouvés')
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_orphelins.html.twig')
            ->context(
                [
                    'enfants' => $enfants,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    /**
     * @param array|Tuteur[] $tuteurs
     */
    public function messageTuteurArchived(array $tuteurs): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject('Les tuteurs ont été archivés')
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_tuteurs_archived.html.twig')
            ->context(
                [
                    'tuteurs' => $tuteurs,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageAlert(string $subject, string $texte): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject($subject)
            ->content($texte);

        return $message;
    }

    public function messageToJf(string $subject, string $texte): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to('jf@marche.be')
            ->subject($subject)
            ->content($texte);

        return $message;
    }
}
