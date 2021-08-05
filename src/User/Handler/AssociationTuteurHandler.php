<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use AcMarche\Mercredi\Mailer\UserMailer;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use function count;

final class AssociationTuteurHandler
{
    private TuteurRepository $tuteurRepository;
    private UserMailer $userMailer;
    private FlashBagInterface $flashBag;
    private UserFactory $userFactory;

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

    public function suggestTuteur(User $user, AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
        if (null !== $tuteur) {
            $associateUserTuteurDto->setTuteur($tuteur);
        }
    }

    public function handleAssociateTuteur(AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        $tuteur = $associateUserTuteurDto->getTuteur();
        $user = $associateUserTuteurDto->getUser();

        if (count($this->tuteurRepository->getTuteursByUser($user)) > 0) {
            //remove old tuteur
            $user->getTuteurs()->clear();
        }

        $user->addTuteur($tuteur);
        $this->tuteurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserTuteurDto->isSendEmail()) {
            try {
                $this->userMailer->sendNewAccountToTuteur($user, $tuteur);
                $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
            }
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

        try {
            $this->userMailer->sendNewAccountToTuteur($user, $tuteur, $plainPassword);
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
        }

        return $user;
    }
}
