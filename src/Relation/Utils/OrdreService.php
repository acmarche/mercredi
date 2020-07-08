<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

class OrdreService
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(RelationRepository $relationRepository, PresenceRepository $presenceRepository)
    {
        $this->relationRepository = $relationRepository;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * Ordre de l'enfant par importance decroissante.
     *
     * @param Presence $presence
     */
    public function getOrdreEnfant(Enfant $enfant, Tuteur $tuteur): int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if ($relation) {
            if ($ordre = $relation->getOrdre()) {
                return $ordre;
            }
        }

        return $enfant->getOrdre();
    }

    /**
     * @throws \Exception
     */
    public function getOrdreOnPresence(PresenceInterface $presence): float
    {
        //on a forcé l'ordre
        if ($ordre = $presence->getOrdre()) {
            return $ordre;
        }

        $tuteur = $presence->getTuteur();
        $enfant = $presence->getEnfant();
        $ordreBase = $this->getOrdreEnfant($enfant, $tuteur);
        //quand enfant premier, fraterie pas d'importance
        if (1 == $ordreBase) {
            return $ordreBase;
        }

        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $enfant,
            [$tuteur]
        );

        if (0 == \count($fratries)) {//pas de fraterie ce jour là
            return $ordreBase;
        }

        $jour = $presence->getJour();

        $presents = [];
        foreach ($fratries as $fratry) {
            if ($this->presenceRepository->findPresencesByEnfantAndJour($fratry, $jour)) {
                $presents[] = $fratry;
            }
        }

        if (0 == \count($presents)) {
            return $ordreBase;
        }

        return $ordreBase - \count($presents); //todo verifier calcul

        //lisa = 2, si marie en 1 reste 2
        //lisa = 3, si marie en 1 devient 2
        foreach ($presents as $frere) {
            $ordreFrere = $this->ordreService->getOrdreEnfant($frere, $tuteur);
        }

        //si ordre enfant = 1, peu importe
        //si ordre enfant = 2, doit avoir 1 fratrie
        //si ordre enfant = 3, doit avoir 2 fratries
        //si ordre enfant = 4, doit avoir 3 fratries
        /*  if ($ordre_fiche > 1) {
              if (($ordre_fiche - 1) == $fratries_count) {
                  //var_dump("ok");
              } else {
                  //var_dump("ko");
              }
          }*/

        return $ordreBase;
    }
}
