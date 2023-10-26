<?php

namespace AcMarche\Mercredi\User\Dto;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssociateUserTuteurDto
{
    public ?Tuteur $tuteur = null;
    public bool $sendEmail = true;

    public function __construct(
        public UserInterface $user
    ) {
    }

}
