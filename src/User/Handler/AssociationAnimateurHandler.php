<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Mailer\Factory\UserEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use function count;

final class AssociationAnimateurHandler
{
    private AnimateurRepository $animateurRepository;
    private FlashBagInterface $flashBag;
    private UserFactory $userFactory;
    private NotificationMailer $notificationMailer;
    private UserEmailFactory $userEmailFactory;

    public function __construct(
        AnimateurRepository $animateurRepository,
        UserFactory $userFactory,
        NotificationMailer $notificationMailer,
        UserEmailFactory $userEmailFactory,
        FlashBagInterface $flashBag
    ) {
        $this->animateurRepository = $animateurRepository;
        $this->flashBag = $flashBag;
        $this->userFactory = $userFactory;
        $this->notificationMailer = $notificationMailer;
        $this->userEmailFactory = $userEmailFactory;
    }

    public function suggestAnimateur(User $user, AssociateUserAnimateurDto $associateUserAnimateurDto): void
    {
        $animateur = $this->animateurRepository->findOneByEmail($user->getEmail());
        if (null !== $animateur) {
            $associateUserAnimateurDto->setAnimateur($animateur);
        }
    }

    public function handleAssociateAnimateur(AssociateUserAnimateurDto $associateUserAnimateurDto): void
    {
        $animateur = $associateUserAnimateurDto->getAnimateur();
        $user = $associateUserAnimateurDto->getUser();

        if ((is_countable($this->animateurRepository->getAnimateursByUser($user)) ? count($this->animateurRepository->getAnimateursByUser($user)) : 0) > 0) {
            //remove old animateur
            $user->getAnimateurs()->clear();
        }

        $user->addAnimateur($animateur);
        $this->animateurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserAnimateurDto->isSendEmail()) {
            $message = $this->userEmailFactory->messageNewAccountToAnimateur($user, $animateur);
            $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
            $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
        }
    }

    public function handleDissociateAnimateur(User $user, Animateur $animateur): Animateur
    {
        $user->removeAnimateur($animateur);

        $this->animateurRepository->flush();
        $this->flashBag->add('success', 'L\'animateur a bien été dissocié.');

        return $animateur;
    }

    public function handleCreateUserFromAnimateur(Animateur $animateur): ?User
    {
        $user = $this->userFactory->newFromAnimateur($animateur);
        $plainPassword = $user->getPlainPassword();

        $message = $this->userEmailFactory->messageNewAccountToAnimateur($user, $animateur, $plainPassword);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        return $user;
    }
}
