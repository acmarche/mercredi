<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', ['form_themes' => ['bootstrap_4_layout.html.twig']]);

    $containerConfigurator->extension('twig',
        [
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/admin' => 'AcMarcheMercrediAdmin',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/parent' => 'AcMarcheMercrediParent',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/animateur' => 'AcMarcheMercrediAnimateur',
                '%kernel.project_dir%/src/AcMarche/Mercredi/templates/ecole' => 'AcMarcheMercrediAnimateur',
            ],
        ]
    );

    $containerConfigurator->extension('twig',
        [
            'globals' => [
                'presence_nb_days' => '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%',
                'pedagogique_nb_days' => '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%',
            ],
        ]
    );
};
