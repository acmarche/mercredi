<?php


namespace AcMarche\Mercredi\Data;


class MercrediConstantes
{
    const SEXES = ['Masculin' => 'Masculin', 'Féminin' => 'Féminin'];
    const ORDRES = [1 => 1, 2 => 2, 'Suivant' => 3];
    const COLORS = [
        'Bleu' => '#a4bdfc',
        'Bleu foncé' => '#5484ed',
        'Gris' => '#e1e1e1',
        'Jaune' => '#fbd75b',
        'Mauve' => '#dbadff',
        'Orange' => '#ffb878',
        'Rouge' => '#ff887c',
        'Rouge foncé' => '#dc2127',
        'Turquoise' => '#46d6db',
        'Vert' => '#7bd148',
        'Vert clair' => '#7ae7bf',
        'Vert foncé' => '#51b749',
    ];

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

}
