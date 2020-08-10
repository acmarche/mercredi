<?php


namespace AcMarche\Mercredi\Ecole\Utils;


use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Common\Collections\ArrayCollection;

class EcoleUtils
{
    /**
     * @param User $user
     * @return Ecole[]|ArrayCollection
     */
    public function getEcolesByUser(User $user): iterable
    {
        return $user->getEcoles();
    }

}
