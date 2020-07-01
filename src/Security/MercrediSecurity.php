<?php

namespace AcMarche\Mercredi\Security;

class MercrediSecurity
{
    const ROLE_ADMIN = 'ROLE_MERCREDI_ADMIN';
    const ROLE_PARENT = 'ROLE_MERCREDI_PARENT';
    const ROLE_ECOLE = 'ROLE_MERCREDI_ECOLE';
    const ROLE_ANIMATEUR = 'ROLE_MERCREDI_ANIMATEUR';

    const ROLES = [
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_PARENT => 'Parent',
        self::ROLE_ECOLE => 'Ecole',
        self::ROLE_ANIMATEUR => 'Animateur',
    ];
}
