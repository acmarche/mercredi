<?php

namespace AcMarche\Mercredi\User\Handler;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Dto\AssociateUserEcoleDto;
use AcMarche\Mercredi\User\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

use function count;

final class AssociationEcoleHandler
{
    private FlashBagInterface $flashBag;
    private UserFactory $userFactory;
    private EcoleRepository $ecoleRepository;

    public function __construct(
        EcoleRepository $ecoleRepository,
        UserFactory $userFactory,
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
        $this->userFactory = $userFactory;
        $this->ecoleRepository = $ecoleRepository;
    }

    public function handleAssociateEcole(AssociateUserEcoleDto $associateUserEcoleDto): void
    {
        $ecoles = $associateUserEcoleDto->getEcoles();
        $user = $associateUserEcoleDto->getUser();

        if (count($this->ecoleRepository->getEcolesByUser($user)) > 0) {
            //remove old ecoles
            $user->getEcoles()->clear();
        }

        foreach ($ecoles as $ecole) {
            $user->addEcole($ecole);
        }

        $this->ecoleRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');
    }

    public function handleDissociateEcole(User $user, Ecole $ecole): Ecole
    {
        $user->removeEcole($ecole);

        $this->ecoleRepository->flush();
        $this->flashBag->add('success', 'L\'école a bien été dissociée.');

        return $ecole;
    }
}
