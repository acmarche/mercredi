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
        ->global('mercredi_register', '%env(MERCREDI_REGISTER)%')
        ->global('mercredi_accueil', '%env(MERCREDI_ACCUEIL)%')
        ->global('mercredi_paiement', '%env(MERCREDI_PAIEMENT)%')
        ->global('mercredi_add_enfant', '%env(MERCREDI_ADD_ENFANT)%')
        ->global('presence_nb_days', '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%',)
        ->global('pedagogique_nb_days', '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%',)
        ->global(
            'bootcdn',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        );
};
