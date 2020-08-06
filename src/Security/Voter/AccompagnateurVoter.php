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
final class AccompagnateurVoter extends Voter
{
    public const INDEX = 'index_accompagnateur';
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    /**
     * @var User
     */
    private $user;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
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
    /**
     * @var string
     */
    private const ECOLE = 'ROLE_MERCREDI_ECOLE';

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, FlashBagInterface $flashBag)
    {
        $this->decisionManager = $accessDecisionManager;
        $this->flashBag = $flashBag;
    }

    protected function supports($attribute, $subject)
    {
        //a cause de index pas d'ecole defini
        if ($subject && ! $subject instanceof Accompagnateur) {
            return false;
        }

        return \in_array($attribute, [self::INDEX, self::SHOW, self::EDIT, self::DELETE], true);
    }

    protected function voteOnAttribute($attribute, $accompagnateur, TokenInterface $token)
    {
        $this->user = $token->getUser();

        if (! $this->user instanceof User) {
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

        if (! $this->decisionManager->decide($token, [self::ECOLE])) {
            return false;
        }
        return $this->ecoles->contains($this->accompagnateur->getEcole());
    }

    private function canEdit(TokenInterface $token)
    {
        if (! $this->decisionManager->decide($token, [self::ECOLE])) {
            return false;
        }
        return $this->ecoles->contains($this->accompagnateur->getEcole());
    }

    private function canDelete(TokenInterface $token)
    {
        return (bool) $this->canEdit($token);
    }

    private function checkEcoles($token)
    {
        if ($this->decisionManager->decide($token, [self::ECOLE]) && \count($this->ecoles) > 0) {
            return true;
        }

        return false;
    }
}
