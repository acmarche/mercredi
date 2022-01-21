<?php

use AcMarche\Mercredi\Namer\DirectoryNamer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'vich_uploader',
        [
            'mappings' => [
                'mercredi_enfant_image' => [
                    'uri_prefix' => '/files/enfants',
                    'upload_destination' => '%kernel.project_dir%/public/files/enfants',
                    'directory_namer' => [
                        'service' => DirectoryNamer::class,
                    ],
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
                'mercredi_organisation_image' => [
                    'uri_prefix' => '/files/organisation',
                    'upload_destination' => '%kernel.project_dir%/public/files/organisation',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
                'mercredi_document' => [
                    'uri_prefix' => '/files/documents',
                    'upload_destination' => '%kernel.project_dir%/public/files/documents',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
                'mercredi_groupe' => [
                    'uri_prefix' => '/files/groupes',
                    'upload_destination' => '%kernel.project_dir%/public/files/groupes',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
            ],
        ]
    );
};
