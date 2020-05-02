<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

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
    const ADD = 'new';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Presence && in_array(
                $attribute,
                [self::ADD, self::SHOW, self::EDIT, self::DELETE]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $presence, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
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

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_READ'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_PARENT'])) {
            return $this->checkTuteur($presence, $token);
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

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_PARENT'])) {
            return $this->checkTuteur($presence, $token);
        }

        return false;
    }

    private function checkTuteur(Presence $presence, TokenInterface $token)
    {
        $user = $token->getUser();
        /**
         * @var Tuteur
         */
        $tuteur = $user->getTuteur();
        $tuteurPresence = $presence->getTuteur();
        if (!$tuteurPresence) {
            return false;
        }

        if ($tuteur === $tuteurPresence) {
            return true;
        }

        return false;
    }
}
