<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use AcMarche\Mercredi\User\Mailer\UserMailer;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AssociationHandler
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var UserMailer
     */
    private $userMailer;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UserFactory
     */
    private $userFactory;

    public function __construct(
        TuteurRepository $tuteurRepository,
        UserMailer $userMailer,
        UserFactory $userFactory,
        FlashBagInterface $flashBag
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->userMailer = $userMailer;
        $this->flashBag = $flashBag;
        $this->userFactory = $userFactory;
    }

    public function suggestTuteur(User $user, AssociateUserTuteurDto $dto): void
    {
        $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
        if ($tuteur) {
            $dto->setTuteur($tuteur);
        }
    }

    public function handleAssociateParent(AssociateUserTuteurDto $dto): void
    {
        $tuteur = $dto->getTuteur();
        $user = $dto->getUser();

        if (\count($this->tuteurRepository->getTuteursByUser($user)) > 0) {
            //remove old tuteur
            $user->getTuteurs()->clear();
        }

        $tuteur->addUser($dto->getUser());
        $this->tuteurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($dto->isSendEmail()) {
            try {
                $this->userMailer->sendNewAccountToParent($user, $tuteur);
                $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
            }
        }
    }

    public function handleDissociateParent(User $user, Tuteur $tuteur)
    {
        $user->removeTuteur($tuteur);

        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'Le parent a bien été dissocié.');

        return $tuteur;
    }

    public function handleCreateUserFromTuteur(Tuteur $tuteur): User
    {
        $user = $this->userFactory->newFromTuteur($tuteur);
        $password = $user->getPlainPassword();
        $this->userMailer->sendNewAccountToParent($user, $tuteur, $password);

        return $user;
    }

    private function dissociateParent(User $user, Tuteur $tuteur): void
    {
        $tuteur->removeUser($user);
        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'L\'utilisateur a bien été dissocié.');
    }
}
