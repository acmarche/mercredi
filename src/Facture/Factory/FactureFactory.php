<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;

class FactureFactory
{
    public function newInstance(Tuteur $tuteur): Facture
    {
        $facture = new Facture($tuteur);
        $facture->setFactureLe(new \DateTime());
        $facture->setTuteurNom($tuteur->getNom());
        $facture->setTuteurPrenom($tuteur->getPrenom());
        $facture->setRue($tuteur->getRue());
        $facture->setCodePostal($tuteur->getCodePostal());
        $facture->setLocalite($tuteur->getLocalite());

        return $facture;
    }

}
