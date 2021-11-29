<?php


namespace AcMarche\Mercredi\Ecole\Utils;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;
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

    /**
     * @param Ecole[]|ArrayCollection $ecoles
     * @return string
     */
    public static function getNamesEcole(iterable $ecoles): string
    {
        $noms = array_map(
            fn ($ecole) => $ecole->getNom(),
            $ecoles->toArray()
        );
        return implode(",", $noms);
    }
}
