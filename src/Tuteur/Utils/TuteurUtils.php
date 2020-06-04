<?php

namespace AcMarche\Mercredi\Tuteur\Utils;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

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

    public static function getEmailsOfOneTuteur(Tuteur $tuteur): array
    {
        $emails = [];

        if (filter_var($tuteur->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $tuteur->getEmail();
        }

        //todo user
        /*   if (filter_var($tuteur->getUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
               $emails[] = $tuteur->getUser()->getEmail();
           }*/

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
