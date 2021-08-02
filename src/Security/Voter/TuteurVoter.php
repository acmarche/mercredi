<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 */
final class TuteurVoter extends Voter
{
    public const ADD = 'tuteur_new';
    public const SHOW = 'tuteur_show';
    public const EDIT = 'tuteur_edit';
    public const DELETE = 'tuteur_delete';

    /**
     * @var User
     */
    private $user;

    private ?Tuteur $tuteurOfUser = null;

    /**
     * @var Tuteur
     */
    private $tuteurToCheck;

    private Security $security;
    private TuteurUtils $tuteurUtils;

    public function __construct(Security $security, TuteurUtils $tuteurUtils)
    {
        $this->security = $security;
        $this->tuteurUtils = $tuteurUtils;
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Tuteur) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $tuteur, TokenInterface $token): bool
    {
        $this->user = $token->getUser();

        if (!$this->user instanceof User) {
            return false;
        }

        $this->tuteurToCheck = $tuteur;

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

        switch ($attribute) {
            case self::SHOW:
                return $this->canShow();
            case self::ADD:
                return $this->canAdd();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    private function canShow(): bool
    {
        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ANIMATEUR)) {
            return true;
        }

        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        return $this->checkOwnTuteur();
    }

    private function canAdd(): bool
    {
        return (bool)$this->canEdit();
    }

    private function canDelete(): bool
    {
        return (bool)$this->canEdit();
    }

    private function checkOwnTuteur(): bool
    {
        if (!$this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return false;
        }

        if (!$this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        if (!$this->tuteurToCheck instanceof Tuteur) {
            return false;
        }

        return $this->tuteurOfUser->getId() === $this->tuteurToCheck->getId();
    }
}
