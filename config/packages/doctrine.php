<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Mercredi' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/AcMarche/Mercredi/src/Entity',
                        'prefix' => 'AcMarche\Mercredi',
                        'alias' => 'AcMarche\Mercredi',
                    ],
                ],
            ],
        ]
    );
};
