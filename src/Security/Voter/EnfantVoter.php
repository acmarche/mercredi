<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
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
class EnfantVoter extends Voter
{
    const ADD = 'enfant_new';
    const ADD_PRESENCE = 'add_presence';
    const SHOW = 'enfant_show';
    const EDIT = 'enfant_edit';
    const DELETE = 'enfant_delete';

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
    private $tuteurOfUser;
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

    public function __construct(
        RelationRepository $relationRepository,
        TuteurUtils $tuteurUtils,
        Security $security
    ) {
        $this->tuteurUtils = $tuteurUtils;
        $this->security = $security;
        $this->relationRepository = $relationRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($subject) {
            if (!$subject instanceof Enfant) {
                return false;
            }
        }

        return in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE, self::ADD_PRESENCE]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $enfant, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->enfant = $enfant;

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_ADMIN')) {
            return true;
        }

        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

        switch ($attribute) {
            case self::SHOW:
                return $this->canView();
            case self::ADD:
                return $this->canAdd();
            case self::ADD_PRESENCE:
                return $this->canAddPresence();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    private function canView()
    {
        if ($this->security->isGranted('ROLE_MERCREDI_READ')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_ANIMATEUR')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_ECOLE')) {
            return false;
        }

        return $this->canEdit();
    }

    private function canEdit()
    {
        return $this->checkTuteur();
    }

    private function canAdd()
    {
        return $this->canEdit();
    }

    private function canAddPresence()
    {
        return $this->canEdit();
    }

    private function canDelete()
    {
        return $this->canEdit();
    }

    /**
     * @return bool
     */
    private function checkTuteur()
    {
        if (!$this->security->isGranted('ROLE_MERCREDI_PARENT')) {
            return false;
        }

        if (!$this->tuteurOfUser) {
            return false;
        }

        $relations = $this->relationRepository->findByTuteur($this->tuteurOfUser);

        $enfants = array_map(
            function ($relation) {
                return $relation->getEnfant()->getId();
            },
            $relations
        );

        if (in_array($this->enfant->getId(), $enfants)) {
            return true;
        }

        return false;
    }
}
