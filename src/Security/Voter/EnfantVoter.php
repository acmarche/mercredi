<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 */
final class EnfantVoter extends Voter
{
    public const ADD = 'enfant_new';
    public const SHOW = 'enfant_show';
    public const EDIT = 'enfant_edit';
    public const DELETE = 'enfant_delete';

    private UserInterface $user;
    private Enfant $enfant;
    private TuteurUtils $tuteurUtils;
    private Security $security;
    private RelationRepository $relationRepository;
    private EnfantRepository $enfantRepository;

    public function __construct(
        RelationRepository $relationRepository,
        TuteurUtils $tuteurUtils,
        EnfantRepository $enfantRepository,
        Security $security
    ) {
        $this->tuteurUtils = $tuteurUtils;
        $this->security = $security;
        $this->relationRepository = $relationRepository;
        $this->enfantRepository = $enfantRepository;
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Enfant) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $enfant, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof User) {
            return false;
        }

        $this->user = $token->getUser();
        $this->enfant = $enfant;

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
        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ECOLE) && $this->checkEcoles()) {
            return true;
        }
        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ANIMATEUR) && $this->checkAnimateur()) {
            return true;
        }

        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        if ($this->security->isGranted(MercrediSecurityRole::ROLE_ECOLE) && $this->checkEcoles()) {
            return true;
        }

        return $this->security->isGranted(MercrediSecurityRole::ROLE_PARENT) && $this->checkTuteur();
    }

    private function canAdd(): bool
    {
        return false;//not use
    }

    private function canDelete(): bool
    {
        return false;//only admin
    }

    private function checkAnimateur(): bool
    {
        $animateur = $this->user->getAnimateur();
        if (!$animateur instanceof Animateur) {
            return false;
        }

        $enfants = new ArrayCollection($this->enfantRepository->findAllForAnimateur($animateur));

        return $enfants->contains($this->enfant);
    }

    /**
     * @return bool
     */
    private function checkTuteur(): bool
    {
        if (!$this->security->isGranted(MercrediSecurityRole::ROLE_PARENT)) {
            return false;
        }

        $tuteur = null;
        $tuteur = $this->tuteurUtils->getTuteurByUser($this->user);
        if (!$tuteur instanceof Tuteur) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($tuteur);

        $enfants = array_map(
            fn($relation) => $relation->getEnfant()->getId(),
            $relations
        );

        return \in_array($this->enfant->getId(), $enfants, true);
    }

    private function checkEcoles(): bool
    {
        $ecoles = $this->user->getEcoles();
        if (\count($ecoles) == 0) {
            return false;
        }

        return $ecoles->contains($this->enfant->getEcole());
    }
}
