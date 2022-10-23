<?php

namespace AcMarche\Mercredi\Plaine\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;

class PdfPlaine
{
    public Plaine $plaine;
    /**
     * @var array|Jour[] $jours
     */
    public array $jours;
    /**
     * @var array|GroupeScolaire[] $groupes
     */
    public array $groupes;

    public \DateTimeInterface $firstDay;

    public function __construct(Plaine $plaine, array $jours, \DateTimeInterface $firstDay)
    {
        $this->plaine = $plaine;
        $this->jours = $jours;
        $this->firstDay = $firstDay;
    }

    public function addGroupe(GroupeScolaire $groupe)
    {
        $this->groupes[$groupe->getId()] = $groupe;
    }

    public function addEnfant(GroupeScolaire $groupeScolaire, Enfant $enfant, Jour $jour)
    {
        $this->addEnfantToGroupe($groupeScolaire, $enfant);
        $this->addJourToEnfant($groupeScolaire, $enfant, $jour);
    }

    private function addEnfantToGroupe(GroupeScolaire $groupeScolaire, Enfant $enfant)
    {
        if (!isset($this->groupes[$groupeScolaire->getId()])) {
            $this->addGroupe($groupeScolaire);
        }
        $this->groupes[$groupeScolaire->getId()]->enfants[$enfant->getId()] = $enfant;
    }

    private function addJourToEnfant(GroupeScolaire $groupeScolaire, Enfant $enfant, Jour $jour)
    {
        $enfant = $this->groupes[$groupeScolaire->getId()]->enfants[$enfant->getId()];
        $enfant->jours[$jour->getId()] = $jour;
    }

}
