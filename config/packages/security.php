<?php

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Authenticator\MercrediAuthenticator;
use AcMarche\Mercredi\Security\Authenticator\MercrediLdapAuthenticator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {
    $security->provider('mercredi_user_provider', [
        'entity' => [
            'class' => User::class,
            'property' => 'username',
        ],
    ]);

    $authenticators = [MercrediAuthenticator::class];
    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = MercrediLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    // @see Symfony\Config\Security\FirewallConfig
    $main = [
        'provider' => 'mercredi_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => MercrediAuthenticator::class,
        'custom_authenticators' => $authenticators,
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'remember_me' => [
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ],
    ];

    $security->roleHierarchy('ROLE_MERCREDI_ADMIN', ['ROLE_MERCREDI', 'ROLE_ALLOWED_TO_SWITCH']);
    $security->roleHierarchy('ROLE_MERCREDI_PARENT', ['ROLE_MERCREDI']);
    $security->roleHierarchy('ROLE_MERCREDI_ECOLE', ['ROLE_MERCREDI']);
    $security->roleHierarchy('ROLE_MERCREDI_ANIMATEUR', ['ROLE_MERCREDI']);


    $security->firewall('main', $main)
        ->switchUser();
};
