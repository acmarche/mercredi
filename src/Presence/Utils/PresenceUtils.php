<?php

namespace AcMarche\Mercredi\Presence\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use AcMarche\Mercredi\Utils\SortUtils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class PresenceUtils
{
    private RelationRepository $relationRepository;
    private ScolaireUtils $scolaireUtils;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        ParameterBagInterface $parameterBag,
        RelationRepository $relationRepository,
        ScolaireUtils $scolaireUtils
    ) {
        $this->relationRepository = $relationRepository;
        $this->scolaireUtils = $scolaireUtils;
        $this->parameterBag = $parameterBag;
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

    /**
     * @param Presence[] $presences
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $presences): array
    {
        return array_unique(
            array_map(
                fn($presence) => $presence->getTuteur(),
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

        $data = [];
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
        return array_unique(
            array_map(
                fn($presence) => $presence->getJour(),
                $presences
            ),
            SORT_REGULAR
        );
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
     * @return ArrayCollection|Plaine[]
     */
    public static function extractPlainesFromPresences(array $presences): array
    {
        $plaines = new ArrayCollection();
        array_map(
            function ($presence) use ($plaines) {
                $jour = $presence->getJour();
                if (!$jour) {
                    return null;
                }
                $plaineJour = $jour->getPlaineJour();
                if (null === $plaineJour) {
                    return null;
                }
                $plaine = $plaineJour->getPlaine();
                if (!$plaines->contains($plaine)) {
                    $plaines->add($plaine);
                }
            },
            $presences
        );

        return $plaines->toArray();
    }
}
