<?php


namespace AcMarche\Mercredi\Facture\Utils;


use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Facture\Facture;

class FactureUtils
{
    /**
     * @return array|Enfant[]
     */
    public function getEnfants(Facture $facture): array
    {
        $enfants = [];
        foreach ($facture->getFacturePresences() as $facturePresence) {
            $presence = $facturePresence->getPresence();
            $enfant = $presence->getEnfant();
            $enfants[$enfant->getId()] = $enfant;
        }
        foreach ($facture->getFactureAccueils() as $factureAccueil) {
            $accueil = $factureAccueil->getAccueil();
            $enfant = $accueil->getEnfant();
            $enfants[$enfant->getId()] = $enfant;
        }

        return $enfants;
    }

    /**
     * @return array|Ecole[]
     */
    public function getEcoles(Facture $facture): array
    {
        $ecoles = [];
        foreach ($this->getEnfants($facture) as $enfant) {
            $ecole = $enfant->getEcole();
            $ecoles[$ecole->getId()] = $ecole;
        }

        return $ecoles;
    }
}
