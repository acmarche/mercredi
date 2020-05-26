<?php


namespace AcMarche\Mercredi\Presence\Utils;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Scolaire\ScolaireData;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;

class PresenceUtils
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * @param Presence[] $presences
     * @return array
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
     * @return Enfant[]
     */
    public static function extractEnfants(array $presences, bool $registerRemarques): array
    {
        $enfants = [];
        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            if ($registerRemarques) {
                $remarques = $enfant->getRemarque();
                if ($presence->getRemarque()) {
                    $remarques .= ' (Parent=>) '.$presence->getRemarque();
                }
                $enfant->setRemarque($remarques);
            }
            $enfants[] = $enfant;
        }

        return $enfants;
    }

    /**
     * @param Enfant[] $enfants
     * @return array
     */
    public static function groupByGroupScolaire(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groups[ScolaireData::getGroupeScolaire($enfant)][] = $enfant;
        }

        return $groups;
    }

    public function addTelephonesOnEnfant(array $enfants)
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
