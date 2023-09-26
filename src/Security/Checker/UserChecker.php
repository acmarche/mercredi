<?php

namespace AcMarche\Mercredi\Security\Checker;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (null === $user->getAnimateur()) {
            throw new LockedException();
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }

    public static function check(UserInterface $user): array
    {
        if ($user->getRoles() < 1) {
            return [
                'error' => 'Aucun rôle, veuillez associer un rôle',
                'user' => $user,
            ];
        }
        if ($user->hasRole(MercrediSecurityRole::ROLE_PARENT) && 0 === \count($user->getTuteurs())) {
            return [
                'error' => 'Rôle parent, mais aucun parent associé',
                'user' => $user,
            ];

        }
        if ($user->hasRole(MercrediSecurityRole::ROLE_ANIMATEUR) && 0 === \count($user->getAnimateurs())) {
            return [
                'error' => 'Rôle animateur, mais aucun animateur associé',
                'user' => $user,
            ];

        }
        if ($user->hasRole(MercrediSecurityRole::ROLE_ECOLE) && 0 === \count($user->getEcoles())) {
            return [
                'error' => 'Rôle école, mais aucune école associée',
                'user' => $user,
            ];

        }

        return [];
    }
}
/*
 * # config/packages/security.yaml
security:
    firewalls:
        api:
            pattern: ^/
            user_checker: App\Security\UserChecker
 */
