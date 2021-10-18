<?php


namespace AcMarche\Mercredi\Facture;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;

/**
 * @property array|Ecole[] $ecolesListing
 */
interface FactureInterface
{
    public const OBJECT_PRESENCE = 'presence';
    public const OBJECT_ACCUEIL = 'accueil';
    public const OBJECT_PLAINE = 'plaine';

    public function getId(): ?int;
}
