<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Animateur;
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
final class AnimateurVoter extends Voter
{
    public UserInterface $user;
    public const INDEX = 'animateur_index';
    public const SHOW = 'animateur_show';
    public const ADD = 'animateur_add';
    public const EDIT = 'animateur_edit';
    public const DELETE = 'animateur_delete';

    private Security $security;
    private ?Animateur $animateur = null;
    /**
     * @var Animateur[]|Collection
     */
    private $animateurs;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        //a cause de index pas d'animateur defini
        if ($subject && !$subject instanceof Animateur) {
            return false;
        }

        return \in_array($attribute, [self::INDEX, self::SHOW, self::ADD, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute($attribute, $animateur, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        if (!$this->security->isGranted(MercrediSecurityRole::ROLE_ANIMATEUR)) {
            return false;
        }

        $this->animateur = $animateur;
        $this->animateurs = $this->user->getAnimateurs();

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
            case self::SHOW:
                return $this->canView();
            case self::DELETE:
            case self::ADD:
                return false; //only admin
            case self::EDIT:
                return $this->canEdit();
        }

        return false;
    }

    private function canIndex(): bool
    {
        return $this->checkAnimateurs();
    }

    private function canView(): bool
    {
        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        if (!$this->checkAnimateurs()) {
            return false;
        }
        if (null === $this->animateur) {
            return false;
        }

        return $this->animateurs->contains($this->animateur);
    }

    private function checkAnimateurs(): bool
    {
        return 0 != \count($this->animateurs);
    }
}
