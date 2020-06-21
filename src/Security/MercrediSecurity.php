<?php

namespace AcMarche\Mercredi\Security;

use AcMarche\Mercredi\Entity\Security\User;

class MercrediSecurity
{
    const ROLES = [
        'ROLE_MERCREDI_ADMIN' => 'Administrateur',
        'ROLE_MERCREDI_PARENT' => 'Parent',
        'ROLE_MERCREDI_ECOLE' => 'Ecole',
        'ROLE_MERCREDI_ANIMATEUR' => 'Animateur',
    ];

    public static function getRolesForProfile(User $user): array
    {
        $roles = $user->getRoles();
        if (false !== ($key = array_search('ROLE_USER', $roles))) {
            unset($roles[$key]);
        }
        if ($user->hasRole('ROLE_MERCREDI_ADMIN')) {
            if (false !== ($key = array_search('ROLE_MERCREDI_READ', $roles))) {
                unset($roles[$key]);
            }
        }

        return $roles;
    }
}
