<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\MercrediSecurity;
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
final class AccueilVoter extends Voter
{
    public const ADD = 'accueil_new';
    public const SHOW = 'accueil_show';
    public const EDIT = 'accueil_edit';
    public const DELETE = 'accueil_delete';

    /**
     * @var Security
     */
    private $security;
    /**
     * @var Accueil|null
     */
    private $accueil;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Enfant
     */
    private $enfant;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof Accueil && \in_array(
                $attribute,
                [self::ADD, self::SHOW, self::EDIT, self::DELETE],
                true
            );
    }

    protected function voteOnAttribute($attribute, $accueil, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->accueil = $accueil;

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(MercrediSecurity::ROLE_ADMIN)) {
            return true;
        }

        $this->enfant = $accueil->getEnfant();

        if ($this->security->isGranted(MercrediSecurity::ROLE_ECOLE)) {
            return $this->checkEcoles();
        }

        return false;
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
