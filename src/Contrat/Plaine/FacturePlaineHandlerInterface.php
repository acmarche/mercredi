<?php

namespace AcMarche\Mercredi\Contrat\Plaine;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;

interface FacturePlaineHandlerInterface
{
    /**
     * set mois, nom plaine, plaine.
     */
    public function newInstance(Plaine $plaine, Tuteur $tuteur): FactureInterface;

    public function handleManually(FactureInterface $facture, Plaine $plaine): FactureInterface;
}
