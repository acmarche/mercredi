<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminEmailFactory
{
    use OrganisationPropertyInitTrait;

    /**
     * @param UserInterface|User $user
     * @param Enfant $enfant
     * @return NotificationEmail
     */
    public function messagEnfantCreated(UserInterface $user, Enfant $enfant): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $message
            ->from($user->getEmail())
            ->to($this->organisation->getEmail())
            ->subject('Un enfant a été ajouté par '.$user->getNom().' '.$user->getPrenom())
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_add_enfant.html.twig')
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
     */
    public function messagEnfantsOrphelins(array $enfants): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $message
            ->from($email)
            ->to($email)
            ->subject('Des enfants orphelins ont été trouvés')
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_orphelins.html.twig')
            ->context(
                [
                    'enfants' => $enfants,
                ]
            );

        return $message;
    }

    /**
     * @param array|Tuteur[] $tuteurs
     */
    public function messagTuteurArchived(array $tuteurs): NotificationEmail
    {
        $message = NotificationEmail::asPublicEmail();
        $email = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $message
            ->from($email)
            ->to($email)
            ->subject('Les tuteurs ont été archivés')
            ->textTemplate('@AcMarcheMercrediEmail/admin/_mail_tuteurs_archived.html.twig')
            ->context(
                [
                    'tuteurs' => $tuteurs,
                ]
            );

        return $message;
    }
}
