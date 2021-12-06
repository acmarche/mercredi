<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
final class EcoleVoter extends Voter
{
    public UserInterface $user;
    public const INDEX = 'ecole_index';
    public const SHOW = 'ecole_show';
    public const ADD = 'ecole_add';
    public const EDIT = 'ecole_edit';
    public const DELETE = 'ecole_delete';

    private Security $security;
    private ?Ecole $ecole = null;
    /**
     * @var Ecole[]|Collection
     */
    private iterable $ecoles;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        //a cause de index pas d'ecole defini
        if ($subject && !$subject instanceof Ecole) {
            return false;
        }

        return \in_array($attribute, [self::INDEX, self::SHOW, self::ADD, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute($attribute, $ecole, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        if (!$this->security->isGranted(MercrediSecurityRole::ROLE_ECOLE)) {
            return false;
        }

        $this->ecole = $ecole;
        $this->ecoles = $this->user->getEcoles();

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
            case self::SHOW:
                return $this->canView();
            case self::DELETE:
            case self::ADD:
                return false;//only admin
            case self::EDIT:
                return $this->canEdit();
        }

        return false;
    }

    private function canIndex(): bool
    {
        return $this->checkEcoles();
    }

    private function canView(): bool
    {
        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        if (!$this->checkEcoles()) {
            return false;
        }
        if ($this->ecole === null) {
            return false;
        }

        return $this->ecoles->contains($this->ecole);
    }

    private function checkEcoles(): bool
    {
        return \count($this->ecoles) != 0;
    }
}
