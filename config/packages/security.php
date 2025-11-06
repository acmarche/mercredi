<?php

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\Authenticator\MercrediAuthenticator;
use AcMarche\Mercredi\Security\Authenticator\MercrediLdapAuthenticator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security
        ->provider('mercredi_user_provider')
        ->entity()
        ->class(User::class)
        ->managerName('default');

    $security
        ->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $mainFirewall = $security
        ->firewall('main')
        ->lazy(true);

    $mainFirewall->switchUser();

    $mainFirewall
        ->formLogin()
        ->loginPath('app_login')
        ->rememberMe(true)
        ->enableCsrf(true);

    $mainFirewall
        ->logout()
        ->path('app_logout');

    $authenticators = [MercrediAuthenticator::class];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = MercrediLdapAuthenticator::class;
        $mainFirewall->formLoginLdap([
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ]);
    }

    $mainFirewall
        ->customAuthenticators($authenticators)
        ->provider('mercredi_user_provider')
        ->entryPoint(MercrediAuthenticator::class)
        ->loginThrottling()
        ->maxAttempts(6)
        ->interval('15 minutes');

    $mainFirewall
        ->rememberMe([
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ]);


    $security->roleHierarchy('ROLE_MERCREDI_ADMIN', ['ROLE_MERCREDI', 'ROLE_ALLOWED_TO_SWITCH']);
    $security->roleHierarchy('ROLE_MERCREDI_PARENT', ['ROLE_MERCREDI']);
    $security->roleHierarchy('ROLE_MERCREDI_ECOLE', ['ROLE_MERCREDI']);
    $security->roleHierarchy('ROLE_MERCREDI_ANIMATEUR', ['ROLE_MERCREDI']);
};