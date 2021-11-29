<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/admin' => 'AcMarcheMercrediAdmin',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/parent' => 'AcMarcheMercrediParent',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/ecole' => 'AcMarcheMercrediEcole',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/animateur' => 'AcMarcheMercrediAnimateur',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/email' => 'AcMarcheMercrediEmail',
            ],
            'globals' => [
                'mercredi_register' => '%env(MERCREDI_REGISTER)%',
                'presence_nb_days' => '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%',
                'pedagogique_nb_days' => '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%',
                'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
            ],
        ]
    );
};
