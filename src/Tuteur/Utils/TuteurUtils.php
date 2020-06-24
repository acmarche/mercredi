<?php

namespace AcMarche\Mercredi\Tuteur\Utils;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class TuteurUtils
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    public static function getTelephones(Tuteur $tuteur): string
    {
        $telephones = '';
        $gsm = $tuteur->getGsm();
        $gsmConjoint = $tuteur->getGsmConjoint();
        $telephoneBureau = $tuteur->getTelephoneBureau();
        $telephoneBureauConjoint = $tuteur->getTelephoneBureauConjoint();
        $telephone = $tuteur->getTelephone();
        $telephoneConjoint = $tuteur->getTelephoneConjoint();

        if ($gsm or $gsmConjoint) {
            $telephones .= $gsm.' | '.$gsmConjoint;
        } elseif ($telephoneBureau or $telephoneBureauConjoint) {
            $telephones .= $telephoneBureau.' | '.$telephoneBureauConjoint;
        } else {
            $telephones .= $telephone.' | '.$telephoneConjoint;
        }

        return $telephones;
    }

    public static function coordonneesIsComplete(Tuteur $tuteur)
    {
        if (0 === strlen(self::getTelephones($tuteur))) {
            return false;
        }

        if (!$tuteur->getNom()) {
            return false;
        }

        if (!$tuteur->getPrenom()) {
            return false;
        }

        if (!$tuteur->getRue()) {
            return false;
        }

        if (!$tuteur->getCodePostal()) {
            return false;
        }

        if (!$tuteur->getLocalite()) {
            return false;
        }

        return true;
    }

    /**
     * Retourne un tableau de string contentant les emails.
     *
     * @param Tuteur[] $tuteurs
     *
     * @return array
     */
    public function getEmails(array $tuteurs)
    {
        $emails = [[]];
        foreach ($tuteurs as $tuteur) {
            if ($this->tuteurIsActif($tuteur)) {
                $emails[] = self::getEmailsOfOneTuteur($tuteur);
            }
        }

        $emails = array_merge(...$emails);

        return array_unique($emails);
    }

    public function tuteurIsActif(Tuteur $tuteur): bool
    {
        return count($this->relationRepository->findEnfantsActifs($tuteur)) > 0;
    }

    /**
     * @param UserInterface|User $user
     * @return Tuteur|null
     */
    public function getTuteurByUser(UserInterface $user): ?Tuteur
    {
        $tuteurs = $user->getTuteurs();

        if (0 == count($tuteurs)) {
            return null;
        }

        return $tuteurs[0];
    }

    /**
     * @param Tuteur $tuteur
     * @return string[]
     */
    public static function getEmailsOfOneTuteur(Tuteur $tuteur): array
    {
        $emails = [];

        if (filter_var($tuteur->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $tuteur->getEmail();
        }

        if (count($tuteur->getUsers()) > 0) {
            $users = $tuteur->getUsers();
            $user = $users[0];
            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $emails[] = $user->getEmail();
            }
        }

        if (filter_var($tuteur->getEmailConjoint(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $tuteur->getEmailConjoint();
        }

        return array_unique($emails);
    }

    /**
     * Retourne la liste des tuteurs qui n'ont pas d'emails.
     *
     * @param Tuteur[] $tuteurs
     *
     * @return Tuteur[]
     */
    public function filterTuteursWithOutEmail(array $tuteurs): array
    {
        $data = [];
        foreach ($tuteurs as $tuteur) {
            if ($this->tuteurIsActif($tuteur)) {
                if (0 == count(self::getEmailsOfOneTuteur($tuteur))) {
                    $data[] = $tuteur;
                }
            }
        }

        return $data;
    }
}
