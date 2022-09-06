<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->path('%kernel.project_dir%/src/AcMarche/Mercredi/templates/admin', 'AcMarcheMercrediAdmin',)
        ->path('%kernel.project_dir%/src/AcMarche/Mercredi/templates/parent', 'AcMarcheMercrediParent',)
        ->path('%kernel.project_dir%/src/AcMarche/Mercredi/templates/ecole', 'AcMarcheMercrediEcole',)
        ->path('%kernel.project_dir%/src/AcMarche/Mercredi/templates/animateur', 'AcMarcheMercrediAnimateur',)
        ->path('%kernel.project_dir%/src/AcMarche/Mercredi/templates/email', 'AcMarcheMercrediEmail',)
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->global('mercredi_register', filter_var('%env(MERCREDI_REGISTER)%', FILTER_VALIDATE_BOOLEAN))
        ->global('mercredi_accueil', filter_var('%env(MERCREDI_ACCUEIL)%', FILTER_VALIDATE_BOOLEAN))
        ->global('mercredi_paiement', filter_var('%env(MERCREDI_PAIEMENT)%', FILTER_VALIDATE_BOOLEAN))
        ->global('presence_nb_days', '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%',)
        ->global('pedagogique_nb_days', '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%',)
        ->global(
            'bootcdn',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        );
};
