<?php

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Authenticator\MercrediAuthenticator;
use AcMarche\Mercredi\Security\Authenticator\MercrediLdapAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'mercredi_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                    ],
                ],
            ],
        ]
    );

    $authenticators = [MercrediAuthenticator::class];

    $main = [
        'provider' => 'mercredi_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => MercrediAuthenticator::class,
        'switch_user' => true,
        'login_throttling' => [
            'max_attempts' => 6, //per minute...
        ],
    ];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = MercrediLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticator'] = $authenticators;

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
                'ROLE_MERCREDI_ADMIN' => ['ROLE_MERCREDI', 'ROLE_ALLOWED_TO_SWITCH'],
                'ROLE_MERCREDI_PARENT' => ['ROLE_MERCREDI'],
                'ROLE_MERCREDI_ECOLE' => ['ROLE_MERCREDI'],
                'ROLE_MERCREDI_ANIMATEUR' => ['ROLE_MERCREDI'],
            ],
        ]
    );
};
