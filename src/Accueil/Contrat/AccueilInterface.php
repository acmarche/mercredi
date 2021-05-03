<?php


namespace AcMarche\Mercredi\Accueil\Contrat;

interface AccueilInterface
{
    const MATIN = 'Matin';
    const SOIR = 'Soir';
    const HEURES = [self::MATIN => 'Au matin', self::SOIR => 'Au soir'];
}
