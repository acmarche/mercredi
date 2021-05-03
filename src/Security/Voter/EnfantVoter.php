<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Security\MercrediSecurity;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

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

    /**
     * @var User
     */
    private $user;
    /**
     * @var Enfant
     */
    private $enfant;
    /**
     * @var Tuteur
     */
    private $tuteur;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(
        RelationRepository $relationRepository,
        TuteurUtils $tuteurUtils,
        JourRepository $jourRepository,
        Security $security
    ) {
        $this->tuteurUtils = $tuteurUtils;
        $this->security = $security;
        $this->relationRepository = $relationRepository;
        $this->jourRepository = $jourRepository;
    }

    protected function supports($attribute, $subject)
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

    protected function voteOnAttribute($attribute, $enfant, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->enfant = $enfant;

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(MercrediSecurity::ROLE_ADMIN)) {
            return true;
        }

        $this->tuteur = $this->tuteurUtils->getTuteurByUser($this->user);

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
        if ($this->security->isGranted(MercrediSecurity::ROLE_ECOLE)) {
            return $this->checkEcoles();
        }
        if ($this->security->isGranted(MercrediSecurity::ROLE_ANIMATEUR)) {
            return $this->checkAnimateur();
        }
        return $this->canEdit();
    }

    private function canEdit(): bool
    {
        if ($this->security->isGranted(MercrediSecurity::ROLE_ECOLE)) {
            return $this->checkEcoles();
        }
        if ($this->security->isGranted(MercrediSecurity::ROLE_PARENT)) {
            return $this->checkTuteur();
        }

        return false;
    }

    private function canAdd(): bool
    {
        return false;//not use
    }

    private function canDelete(): bool
    {
        return false;//only admin
    }

    private function checkAnimateur()
    {
        $animateur = $this->user->getAnimateur();
        if (!$animateur) {
            return false;
        }

        $enfants = new ArrayCollection($this->jourRepository->findAllForAnimateur($animateur));
        if ($enfants->contains($this->enfant)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function checkTuteur(): bool
    {
        if (!$this->security->isGranted(MercrediSecurity::ROLE_PARENT)) {
            return false;
        }

        if (!$this->tuteur) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($this->tuteur);

        $enfants = array_map(
            function ($relation) {
                return $relation->getEnfant()->getId();
            },
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
