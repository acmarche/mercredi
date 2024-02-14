<?php

namespace AcMarche\Mercredi\Security\Role;

final class MercrediSecurityRole
{
    public const ROLE_ADMIN = 'ROLE_MERCREDI_ADMIN';
    public const ROLE_PARENT = 'ROLE_MERCREDI_PARENT';
    public const ROLE_ECOLE = 'ROLE_MERCREDI_ECOLE';
    public const ROLE_ANIMATEUR = 'ROLE_MERCREDI_ANIMATEUR';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_PARENT => 'Parent',
        self::ROLE_ECOLE => 'Ecole',
        self::ROLE_ANIMATEUR => 'Animateur',
    ];

    public static function niceName(array $roles): array
    {
        $nices = [];
        foreach ($roles as $role) {
            if (isset(self::ROLES[$role])) {
                $nice = self::ROLES[$role];
                $nices[] = $nice;
            }
        }

        return $nices;
    }

    public static function explanation(): array
    {
        $nices = [];
        foreach (self::ROLES as $slug => $role) {
            $nices[$role] = self::description($slug);
        }
        ksort($nices);

        return $nices;
    }

    public static function description(string $role): string
    {
        return match ($role) {
            self::ROLE_PARENT => 'Accède uniquement aux enfants liés à sa fiche parent',
            self::ROLE_ECOLE => 'Accède aux enfants appartenant aux écoles liées à son compte et dont sur la fiche enfant le champ "accueil" est coché (Hotton)',
            self::ROLE_ADMIN => 'Accède à tout',
            self::ROLE_ANIMATEUR => 'Accède aux enfants qui sont présents aux jours où l\'animateur travaille',
        };
    }
}
