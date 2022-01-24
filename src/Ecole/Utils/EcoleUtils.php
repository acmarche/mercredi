<?php

namespace AcMarche\Mercredi\Ecole\Utils;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Security\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EcoleUtils
{
    /**
     * @return Ecole[]|Collection
     */
    public function getEcolesByUser(User $user): iterable
    {
        return $user->getEcoles();
    }

    /**
     * @param Ecole[]|Collection $ecoles
     */
    public static function getNamesEcole(array|Collection $ecoles): string
    {
        $noms = array_map(
            fn ($ecole) => $ecole->getNom(),
            $ecoles->toArray()
        );

        return implode(',', $noms);
    }
}
