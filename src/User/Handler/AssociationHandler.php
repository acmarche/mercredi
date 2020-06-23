<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
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

    public function __construct(TuteurRepository $tuteurRepository, UserMailer $userMailer, FlashBagInterface $flashBag)
    {
        $this->tuteurRepository = $tuteurRepository;
        $this->userMailer = $userMailer;
        $this->flashBag = $flashBag;
    }

    public function suggestTuteur(User $user, AssociateUserTuteurDto $dto)
    {
        $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
        if ($tuteur) {
            $dto->setTuteur($tuteur);
        }
    }

    public function handleAssociateParent(AssociateUserTuteurDto $dto)
    {
        $tuteur = $dto->getTuteur();
        $user = $dto->getUser();

        if (count($this->tuteurRepository->getTuteursByUser($user)) > 0) {
            $user->getTuteurs()->clear(); //remove old tuteur
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

    public function handleDissociateParent(User $user, int $tuteurId)
    {
        $tuteur = $this->tuteurRepository->find($tuteurId);
        $user->removeTuteur($tuteur);

        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'Le parent a bien été dissocié.');
    }

    private function dissociateParent(User $user, Tuteur $tuteur)
    {
        $tuteur->removeUser($user);
        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'L\'utilisateur a bien été dissocié.');
    }

    private function removeCurrentTuteurs(User $user, array $tuteurs)
    {
        foreach ($tuteurs as $tuteur) {
            $user->removeTuteur($tuteur);
        }
    }
}