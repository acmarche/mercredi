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

use function count;

final class AssociationHandler
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
    /**
     * @var string
     */
    private const SUCCESS = 'success';

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
        if ($tuteur !== null) {
            $associateUserTuteurDto->setTuteur($tuteur);
        }
    }

    public function handleAssociateParent(AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        $tuteur = $associateUserTuteurDto->getTuteur();
        $user = $associateUserTuteurDto->getUser();

        if (count($this->tuteurRepository->getTuteursByUser($user)) > 0) {
            //remove old tuteur
            $user->getTuteurs()->clear();
        }

        $tuteur->addUser($associateUserTuteurDto->getUser());
        $this->tuteurRepository->flush();

        $this->flashBag->add(self::SUCCESS, 'L\'utilisateur a bien été associé.');

        if ($associateUserTuteurDto->isSendEmail()) {
            try {
                $this->userMailer->sendNewAccountToParent($user, $tuteur);
                $this->flashBag->add(self::SUCCESS, 'Un mail de bienvenue a été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
            }
        }
    }

    public function handleDissociateParent(User $user, Tuteur $tuteur)
    {
        $user->removeTuteur($tuteur);

        $this->tuteurRepository->flush();
        $this->flashBag->add(self::SUCCESS, 'Le parent a bien été dissocié.');

        return $tuteur;
    }

    public function handleCreateUserFromTuteur(Tuteur $tuteur): User
    {
        $user = $this->userFactory->newFromTuteur($tuteur);
        $plainPassword = $user->getPlainPassword();
        try {
            $this->userMailer->sendNewAccountToParent($user, $tuteur, $plainPassword);
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('danger', 'Erreur lors de l\'envoie du mail: '.$e->getMessage());
        }

        return $user;
    }
}
