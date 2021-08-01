<?php

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Authenticator\MercrediAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'encoders' => [
            User::class => ['algorithm' => 'auto'],
        ],
    ]);

    $containerConfigurator->extension('security', [
            'providers' => [
                'mercredi_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $main = [
        'provider' => 'mercredi_user_provider',
        'custom_authenticator' => MercrediAuthenticator::class,
        'logout' => ['path' => 'app_logout'],
    ];

    if (class_exists(LdapInterface::class)) {
        $main['form_login_ldap'] = [];
    }

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'role_hierarchy' => [
                'ROLE_MERCREDI_ADMIN' => ['ROLE_MERCREDI'],
                'ROLE_MERCREDI_PARENT' => ['ROLE_MERCREDI'],
                'ROLE_MERCREDI_ECOLE' => ['ROLE_MERCREDI'],
                'ROLE_MERCREDI_ANIMATEUR' => ['ROLE_MERCREDI'],
            ],
        ]
    );
};
