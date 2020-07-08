<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Security\User;
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
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class PresenceVoter extends Voter
{
    const ADD = 'presence_new';
    const SHOW = 'presence_show';
    const EDIT = 'presence_edit';
    const DELETE = 'presence_delete';
    private $decisionManager;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;
    /**
     * @var Security
     */
    private $security;
    private $user;
    private $enfant;

    public function __construct(
        RelationRepository $relationRepository,
        TuteurUtils $tuteurUtils,
        Security $security
    ) {
        $this->relationRepository = $relationRepository;
        $this->tuteurUtils = $tuteurUtils;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Presence && \in_array(
                $attribute,
                [self::ADD, self::SHOW, self::EDIT, self::DELETE]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $presence, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->enfant = $presence->getEnfant();

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->security->isGranted( 'ROLE_MERCREDI_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView($presence, $token);
            case self::ADD:
                return $this->canAdd($presence, $token);
            case self::EDIT:
                return $this->canEdit($presence, $token);
            case self::DELETE:
                return $this->canDelete($presence, $token);
        }

        return false;
    }

    private function canView(Presence $presence, TokenInterface $token)
    {
        if ($this->canEdit($presence, $token)) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_READ')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_PARENT')) {
            return $this->checkTuteur();
        }

        return false;
    }

    /**
     * Uniquement l'admin, droit donne plus haut.
     *
     * @return bool
     */
    private function canEdit(Presence $presence, TokenInterface $token)
    {
        if ($this->security->isGranted('ROLE_MERCREDI_PARENT')) {
            return $this->checkTuteur();
        }
        return false;
    }

    private function canAdd(Presence $presence, TokenInterface $token)
    {
        if ($this->canEdit($presence, $token)) {
            return true;
        }

        return false;
    }

    private function canDelete(Presence $presence, TokenInterface $token)
    {
        if ($this->canEdit($presence, $token)) {
            return true;
        }

        if ($this->security->isGranted( 'ROLE_MERCREDI_PARENT')) {
            return $this->checkTuteur();
        }

        return false;
    }

    /**
     * @return bool
     */
    private function checkTuteur()
    {
        if (!$this->security->isGranted('ROLE_MERCREDI_PARENT')) {
            return false;
        }

        $this->tuteurOfUser = $this->tuteurUtils->getTuteurByUser($this->user);

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

        if (\in_array($this->enfant->getId(), $enfants)) {
            return true;
        }

        return false;
    }
}
