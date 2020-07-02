<?php

namespace AcMarche\Mercredi\Presence\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;

class PresenceUtils
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var ScolaireUtils
     */
    private $scolaireUtils;

    public function __construct(RelationRepository $relationRepository, ScolaireUtils $scolaireUtils)
    {
        $this->relationRepository = $relationRepository;
        $this->scolaireUtils = $scolaireUtils;
    }

    /**
     * @param Presence[] $presences
     */
    public function groupByYear(array $presences): array
    {
        $all = [];
        foreach ($presences as $presence) {
            $jour = $presence->getJour()->getDateJour();
            $all[$jour->format('Y')][$jour->format('m')][] = $presence;
        }

        return $all;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $presences): array
    {
        return array_unique(
            array_map(
                function ($presence) {
                    return $presence->getTuteur();
                },
                $presences
            ),
            SORT_REGULAR
        );
    }

    /**
     * @param Presence[] $presences
     *
     * @return Enfant[]
     */
    public static function extractEnfants(array $presences, bool $registerRemarques = false): array
    {
        $enfants =
            array_map(
                function ($presence) use ($registerRemarques) {
                    $enfant = $presence->getEnfant();
                    if ($registerRemarques) {
                        $remarques = $enfant->getRemarque();
                        if ($presence->getRemarque()) {
                            $remarques .= ' (Parent=>) '.$presence->getRemarque();
                        }
                        $enfant->setRemarque($remarques);
                    }

                    return $enfant;
                },
                $presences
            );
        $enfants = array_unique($enfants, SORT_REGULAR);

        return $enfants;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Jour[]
     */
    public static function extractJours(array $presences): array
    {
        return array_unique(
            array_map(
                function ($presence) {
                    return $presence->getJour();
                },
                $presences
            ),
            SORT_REGULAR
        );
    }

    /**
     * @param Enfant[] $enfants
     */
    public function groupByGroupScolaire(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);
            $groups[$groupe->getNom()][] = $enfant;
        }

        return $groups;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function addTelephonesOnEnfants(array $enfants)
    {
        foreach ($enfants as $enfant) {
            $telephones = '';
            foreach ($this->relationRepository->findByEnfant($enfant) as $relation) {
                $tuteur = $relation->getTuteur();
                $telephones .= ' '.TuteurUtils::getTelephones($tuteur);
            }
            $enfant->setTelephones($telephones);
        }
    }
}
