<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use AcMarche\Mercredi\User\Mailer\UserMailer;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use function count;

final class AssociationAnimateurHandler
{
    private AnimateurRepository $animateurRepository;
    private UserMailer $userMailer;
    private FlashBagInterface $flashBag;
    private UserFactory $userFactory;

    public function __construct(
        AnimateurRepository $animateurRepository,
        UserMailer $userMailer,
        UserFactory $userFactory,
        FlashBagInterface $flashBag
    ) {
        $this->animateurRepository = $animateurRepository;
        $this->userMailer = $userMailer;
        $this->flashBag = $flashBag;
        $this->userFactory = $userFactory;
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

        if (count($this->animateurRepository->getAnimateursByUser($user)) > 0) {
            //remove old animateur
            $user->getAnimateurs()->clear();
        }

        $user->addAnimateur($animateur);
        $this->animateurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserAnimateurDto->isSendEmail()) {
            try {
                $this->userMailer->sendNewAccountToAnimateur($user, $animateur);
                $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
            }
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

        try {
            $this->userMailer->sendNewAccountToAnimateur($user, $animateur, $plainPassword);
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
        }

        return $user;
    }
}
