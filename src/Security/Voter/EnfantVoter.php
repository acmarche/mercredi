<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
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
class EnfantVoter extends Voter
{
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
     * @var TokenInterface
     */
    private $token;
    const INDEX = 'index_enfant';
    const ADD = 'new';
    const ADD_PRESENCE = 'add_presence';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AccessDecisionManagerInterface $decisionManager,
        FlashBagInterface $flashBag,
        RouterInterface $router
    ) {
        $this->decisionManager = $decisionManager;
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        //pour tester si tuteur associe a compte
        if ($subject) {
            if (!$subject instanceof Enfant) {
                return false;
            }
        }

        return in_array(
            $attribute,
            [self::INDEX, self::ADD, self::SHOW, self::EDIT, self::DELETE, self::ADD_PRESENCE]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $enfant, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->token = $token;
        $this->enfant = $enfant;

        if (!$this->user instanceof User) {
            return false;
        }

        $this->tuteurOfUser = $this->user->getTuteur();

        if ($this->decisionManager->decide($token, ['ROLE_MERCREDI_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
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

    private function canIndex()
    {
        if (!$this->tuteurOfUser instanceof Tuteur) {
            $this->flashBag->add('danger', 'Aucun parent associé à votre compte');

            return false;
        }

        return true;
    }

    private function canView()
    {
        if ($this->decisionManager->decide($this->token, ['ROLE_MERCREDI_READ'])) {
            return true;
        }

        if ($this->decisionManager->decide($this->token, ['ROLE_MERCREDI_ANIMATEUR'])) {
            return true;
        }

        if ($this->decisionManager->decide($this->token, ['ROLE_MERCREDI_ECOLE'])) {
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
     *
     * @throws RedirectException
     */
    private function checkTuteur()
    {
        if (!$this->decisionManager->decide($this->token, ['ROLE_MERCREDI_PARENT'])) {
            return false;
        }

        $tuteur = $this->user->getTuteur();

        if (!$tuteur) {
            $this->flashBag->add('danger', 'Aucun parent associé à votre compte');
            throw new RedirectException(new RedirectResponse($this->router->generate('parent_nouveau')));
        }

        /**
         * @var EnfantTuteur[]
         */
        $enfant_tuteurs = $tuteur->getEnfants();
        $enfants = [];

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $enfants[] = $enfant_tuteur->getEnfant()->getId();
        }

        $enfant_id = $this->enfant->getId();
        if (in_array($enfant_id, $enfants)) {
            return true;
        }

        return false;
    }
}
