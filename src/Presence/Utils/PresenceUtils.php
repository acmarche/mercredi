<?php

namespace AcMarche\Mercredi\Presence\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use AcMarche\Mercredi\Utils\SortUtils;
use Carbon\Carbon;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class PresenceUtils
{
    public const types = [
        'Mercredi et Plaines' => self::mercredi_plaine,
        'Mercredi' => self::mercredi,
        'Plaines' => self::plaine,
    ];
    public const mercredi = null;
    public const plaine = true;
    public const mercredi_plaine = false;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private RelationRepository $relationRepository
    ) {
    }

    public function getDeadLineDatePresence(): Carbon
    {
        $today = Carbon::today();
        $today->addDays($this->parameterBag->get(Option::PRESENCE_DEADLINE_DAYS));

        return $today;
    }

    public function getDeadLineDatePedagogique(): Carbon
    {
        $today = Carbon::today();
        $today->addDays($this->parameterBag->get(Option::PEDAGOGIQUE_DEADLINE_DAYS));

        return $today;
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

    public static function groupByQuarter(array $presences): array
    {
        $all = [1 => [], 2 => [], 3 => [], 4 => []];
        foreach ($presences as $presence) {
            $jour = $presence->getJour()->getDateJour();
            $monthNumber = $jour->format('n');
            $i = match (true) {
                $monthNumber < 4 => 1,
                $monthNumber < 7 => 2,
                $monthNumber < 10 => 3,
                default => 4
            };
            $all[$i][] = $presence;
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
        $tuteurs = array_map(
            fn($presence) => $presence->getTuteur(),
            $presences
        );
        $data = [];
        $tuteurs = SortUtils::sortByName($tuteurs);
        foreach ($tuteurs as $tuteur) {
            $data[$tuteur->getId()] = $tuteur;
        }

        return $data;
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

        $data = [];
        $enfants = SortUtils::sortByName($enfants);
        foreach ($enfants as $enfant) {
            $data[$enfant->getId()] = $enfant;
        }

        return $data;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Jour[]
     */
    public static function extractJours(array $presences): array
    {
        $jours =
            array_map(
                fn($presence) => $presence->getJour(),
                $presences
            );
        $data = [];
        foreach ($jours as $jour) {
            $data[$jour->getId()] = $jour;
        }

        return $data;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function addTelephonesOnEnfants(array $enfants): void
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

    /**
     * @param Presence[] $presences
     *
     * @return Plaine[]
     */
    public static function extractPlainesFromPresences(array $presences): array
    {
        $plaines = [];
        foreach ($presences as $presence) {
            $jour = $presence->getJour();
            if (!$jour instanceof Jour) {
                continue;
            }
            $plaine = $jour->getPlaine();
            if (!$plaine instanceof Plaine) {
                continue;
            }
            $plaines[$plaine->getId()] = $plaine;
        }

        return $plaines;
    }

    /**
     * @param Presence[] $presences
     */
    public static function groupByTuteur(array $presences): array
    {
        $data = [];
        foreach ($presences as $presence) {
            $tuteur = $presence->getTuteur();
            $data[$tuteur->getId()]['tuteur'] = $tuteur;
            $data[$tuteur->getId()]['presences'][] = $presence;
        }

        return $data;
    }
}
