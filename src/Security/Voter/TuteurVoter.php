<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Entity\User;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
class TuteurVoter extends Voter
{
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
     * @var TokenInterface
     */
    private $token;
    const INDEX = 'index_tuteur';
    const ADD = 'new';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(AccessDecisionManagerInterface $decisionManager, FlashBagInterface $flashBag)
    {
        $this->decisionManager = $decisionManager;
        $this->flashBag = $flashBag;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        //pour tester si tuteur associe a compte
        if ($subject) {
            if (!$subject instanceof Tuteur) {
                return false;
            }
        }

        return in_array(
            $attribute,
            [self::INDEX, self::ADD, self::SHOW, self::EDIT, self::DELETE]
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

        $this->token = $token;
        $this->tuteurToCheck = $tuteur;
        $this->tuteurOfUser = $this->user->getTuteur();

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
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

    /**
     * Utiliser par parent car tuteur pas en parametre
     * mais via user->getTuteur.
     *
     * @return bool
     */
    private function canIndex()
    {
        if (!$this->tuteurOfUser instanceof Tuteur) {
            $this->flashBag->add('danger', 'Aucun parent associé à votre compte');

            return false;
        }

        return true;
    }

    private function canShow()
    {
        if ($this->decisionManager->decide($this->token, ['ROLE_MERCREDI_READ'])) {
            return true;
        }

        if ($this->decisionManager->decide($this->token, ['ROLE_MERCREDI_ANIMATEUR'])) {
            return true;
        }

        return $this->canEdit();
    }

    private function canEdit()
    {
        return $this->checkTuteur();
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

    private function checkTuteur()
    {
        if (!$this->decisionManager->decide($this->token, ['ROLE_MERCREDI_PARENT'])) {
            return false;
        }

        if (!$this->tuteurOfUser instanceof Tuteur) {
            return false;
        }

        if (!$this->tuteurToCheck instanceof Tuteur) {
            return false;
        }

        if ($this->tuteurOfUser === $this->tuteurToCheck) {
            return true;
        }

        return false;
    }
}
