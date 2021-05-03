<?php


namespace AcMarche\Mercredi\Security\Checker;

use AcMarche\Mercredi\Entity\Security\User;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getAnimateur()) {
            throw new LockedException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
/**
 * # config/packages/security.yaml
security:
    firewalls:
        api:
            pattern: ^/
            user_checker: App\Security\UserChecker
 */
