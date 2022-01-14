<?php

namespace AcMarche\Mercredi\Accueil\Contrat;

interface AccueilInterface
{
    public const MATIN = 'Matin';
    public const SOIR = 'Soir';
    public const HEURES = [self::MATIN => 'Au matin', self::SOIR => 'Au soir'];
}
