<?php

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Authenticator\MercrediAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
                'ville_ldap' => [
                    'ldap' => [
                        'service' => 'Symfony\Component\Ldap\Ldap',
                        'base_dn' => '%env(ACLDAP_DN)%',
                        'search_dn' => '%env(ACLDAP_USER)%',
                        'search_password' => '%env(ACLDAP_PASSWORD)%',
                        'default_roles' => 'ROLE_BOTTIN_ADMIN',
                        'uid_key' => 'sAMAccountName',
                        'extra_fields' => ['mail'],
                    ],
                ],
                'all_users' => [
                    'chain' => [
                        'providers' => ['ville_ldap', 'mercredi_user_provider'],
                    ],
                ],
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => [
                    'provider' => 'all_users',
                    'custom_authenticator' => MercrediAuthenticator::class,
                    'form_login_ldap' => [
                        'service' => 'Symfony\Component\Ldap\Ldap',
                        'search_dn' => '%env(ACLDAP_USER)%',
                        'search_password' => '%env(ACLDAP_PASSWORD)%',
                        'query_string' => '(&(|(sAMAccountName={username}))(objectClass=person))',
                        'dn_string' => '%env(ACLDAP_DN)%',
                        'check_path' => 'app_login',
                        'username_parameter' => 'username',
                        'password_parameter' => 'password',
                    ],
                    'logout' => ['path' => 'app_logout'],
                ],
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
