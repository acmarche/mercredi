<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
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
class TuteurVoter extends Voter
{
    const ADD = 'tuteur_new';
    const SHOW = 'tuteur_show';
    const EDIT = 'tuteur_edit';
    const DELETE = 'tuteur_delete';

    /**
     * @var User
     */
    private $user;

    /**
     * @var Tuteur
     */
    private $tuteurOfUser;

    /**
     * @var Tuteur
     */
    private $tuteurToCheck;

    /**
     * @var Security
     */
    private $security;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;

    public function __construct(Security $security, TuteurUtils $tuteurUtils)
    {
        $this->security = $security;
        $this->tuteurUtils = $tuteurUtils;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($subject) {
            if (!$subject instanceof Tuteur) {
                return false;
            }
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $tuteur, TokenInterface $token)
    {
        $this->user = $token->getUser();

        if (!$this->user instanceof User) {
            return false;
        }

        $this->tuteurToCheck = $tuteur;

        if ($this->security->isGranted('ROLE_MERCREDI_ADMIN')) {
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

    private function canShow()
    {
        if ($this->security->isGranted('ROLE_MERCREDI_READ')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_MERCREDI_ANIMATEUR')) {
            return true;
        }

        return $this->canEdit();
    }

    private function canEdit()
    {
        return $this->checkOwnTuteur();
    }

    private function canAdd()
    {
        if ($this->canEdit()) {
            return true;
        }

        return false;
    }

    private function canDelete()
    {
        if ($this->canEdit()) {
            return true;
        }

        return false;
    }

    private function checkOwnTuteur()
    {
        if (!$this->security->isGranted('ROLE_MERCREDI_PARENT')) {
            return false;
        }

        if (!$this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        if (!$this->tuteurToCheck instanceof Tuteur) {
            return false;
        }

        if ($this->tuteurOfUser->getId() === $this->tuteurToCheck->getId()) {
            return true;
        }

        return false;
    }
}
