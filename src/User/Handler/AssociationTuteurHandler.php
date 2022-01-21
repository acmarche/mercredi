<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Mailer\Factory\UserEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AssociationTuteurHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(
        private TuteurRepository $tuteurRepository,
        private UserFactory $userFactory,
        private NotificationMailer $notificationMailer,
        private UserEmailFactory $userEmailFactory,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function suggestTuteur(User $user, AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        try {
            $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
            if (null !== $tuteur) {
                $associateUserTuteurDto->setTuteur($tuteur);
            }
        } catch (NonUniqueResultException) {
        }
    }

    public function handleAssociateTuteur(AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        $tuteur = $associateUserTuteurDto->getTuteur();
        $user = $associateUserTuteurDto->getUser();

        if ([] !== $this->tuteurRepository->getTuteursByUser($user)) {
            //remove old tuteur
            $user->getTuteurs()->clear();
        }

        if (null === $tuteur) {
            $this->flashBag->add('danger', 'Aucun tuteur sélectionné.');

            return;
        }
        $user->addTuteur($tuteur);
        $this->tuteurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserTuteurDto->isSendEmail()) {
            $message = $this->userEmailFactory->messageNewAccountToTuteur($user, $tuteur);
            $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
            $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
        }
    }

    public function handleDissociateTuteur(User $user, Tuteur $tuteur): Tuteur
    {
        $user->removeTuteur($tuteur);

        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'Le parent a bien été dissocié.');

        return $tuteur;
    }

    public function handleCreateUserFromTuteur(Tuteur $tuteur): ?User
    {
        $user = $this->userFactory->newFromTuteur($tuteur);
        $plainPassword = $user->getPlainPassword();

        $message = $this->userEmailFactory->messageNewAccountToTuteur($user, $tuteur, $plainPassword);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
        $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');

        return $user;
    }
}
