<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
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
final class PresenceVoter extends Voter
{
    public const ADD = 'presence_new';
    public const SHOW = 'presence_show';
    public const EDIT = 'presence_edit';
    public const DELETE = 'presence_delete';

    public ?Tuteur $tuteurOfUser = null;
    private RelationRepository $relationRepository;
    private TuteurUtils $tuteurUtils;
    private Security $security;
    private ?UserInterface $user = null;
    private ?Enfant $enfant = null;

    public function __construct(
        RelationRepository $relationRepository,
        TuteurUtils $tuteurUtils,
        Security $security
    ) {
        $this->relationRepository = $relationRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Presence && \in_array(
                $attribute,
                [self::ADD, self::SHOW, self::EDIT, self::DELETE],
                true
            );
    }

    protected function voteOnAttribute($attribute, $presence, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        $this->user = $token->getUser();
        $this->enfant = $presence->getEnfant();

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ADMIN)) {
            return true;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView();
            case self::ADD:
                return $this->canAdd();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    private function canView(): bool
    {
        if ($this->canEdit()) {
            return true;
        }

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return $this->checkTuteur();
        }

        return false;
    }

    /**
     * Uniquement l'admin, droit donne plus haut.
     */
    private function canEdit(): bool
    {
        if ($this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return $this->checkTuteur();
        }

        return false;
    }

    private function canAdd(): bool
    {
        return $this->canEdit();
    }

    private function canDelete(): bool
    {
        if ($this->canEdit()) {
            return true;
        }

        if ($this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return $this->checkTuteur();
        }

        return false;
    }

    private function checkTuteur(): bool
    {
        if (!$this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return false;
        }

        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

        if (!$this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($this->tuteurOfUser);

        $enfants = array_map(
            fn($relation) => $relation->getEnfant()->getId(),
            $relations
        );

        return \in_array($this->enfant->getId(), $enfants, true);
    }
}
