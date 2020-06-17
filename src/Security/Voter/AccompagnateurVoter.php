<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Security\User;
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
class AccompagnateurVoter extends Voter
{
    /**
     * @var User
     */
    private $user;
    const INDEX = 'index_accompagnateur';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $decisionManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var Ecole[]|\Doctrine\Common\Collections\Collection
     */
    private $ecoles;
    /**
     * @var Accompagnateur
     */
    private $accompagnateur;

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
        //a cause de index pas d'ecole defini
        if ($subject) {
            if (!$subject instanceof Accompagnateur) {
                return false;
            }
        }

        return in_array($attribute, [self::INDEX, self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $accompagnateur, TokenInterface $token)
    {
        $this->user = $token->getUser();

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
            return true;
        }

        $this->accompagnateur = $accompagnateur;
        $this->ecoles = $this->user->getEcoles();

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex($token);
            case self::SHOW:
                return $this->canView($token);
            case self::EDIT:
                return $this->canEdit($token);
            case self::DELETE:
                return $this->canDelete($token);
        }

        return false;
    }

    private function canIndex(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        if ($this->checkEcoles($token)) {
            return true;
        }

        $this->flashBag->add('danger', 'Aucune école attribué à votre compte');

        return false;
    }

    private function canView(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        if (!$this->decisionManager->decide($token, ['ROLE_MERCREDI_ECOLE'])) {
            return false;
        }

        if ($this->ecoles->contains($this->accompagnateur->getEcole())) {
            return true;
        }

        return false;
    }

    private function canEdit(TokenInterface $token)
    {
        if (!$this->decisionManager->decide($token, ['ROLE_MERCREDI_ECOLE'])) {
            return false;
        }

        if ($this->ecoles->contains($this->accompagnateur->getEcole())) {
            return true;
        }

        return false;
    }

    private function canDelete(TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        return false;
    }

    private function checkEcoles($token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ECOLE'])) {
            if (count($this->ecoles) > 0) {
                return true;
            }
        }

        return false;
    }
}
