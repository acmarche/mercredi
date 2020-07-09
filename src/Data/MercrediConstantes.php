<?php

namespace AcMarche\Mercredi\Data;

class MercrediConstantes
{
    const SEXES = ['Masculin' => 'Masculin', 'Féminin' => 'Féminin'];
    const ORDRES = ['' => 0, 1 => 1, 2 => 2, 'Suivant' => 3];

    const ABSENCE_NON = 0;
    const ABSENCE_AVEC_CERTIF = 1;
    const ABSENCE_SANS_CERTIF = -1;

    public static function getListAbsences(): array
    {
        return [
            self::ABSENCE_NON => 'Non',
            self::ABSENCE_AVEC_CERTIF => 'Oui avec certificat',
            self::ABSENCE_SANS_CERTIF => 'Oui sans certificat',
        ];
    }

    public static function getAbsenceTxt($number = false): string
    {
        $absences = self::getListAbsences();
        if (!$number) {
            return '';
        }

        return isset($absences[$number]) ? $absences[$number] : $number;
    }

    public static function getCiviliteBySexe(?string $sexe): string
    {
        switch ($sexe) {
            case 'Masculin':
                return 'Monsieur';
                break;
            case 'Féminin':
                return 'Madame';
                break;
            default:
                return '';
                break;
        }
    }

    public static function getMatinApresMidi(): array {
        return ['Matin'=>'Matin','Après-midi'=>'Après-midi'];
    }
}
