<?php

use AcMarche\Mercredi\Namer\DirectoryNamer;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Security\Ldap\LdapMercredi;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\ServiceIterator\AfterUserRegistration;
use AcMarche\Mercredi\ServiceIterator\Register;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::EMAILS_FACTURE, '%env(MERCREDI_EMAILS_FACTURE)%');
    $parameters->set(Option::REGISTER, '%env(MERCREDI_REGISTER)%');
    $parameters->set(Option::ACCUEIL, '%env(MERCREDI_ACCUEIL)%');
    $parameters->set(Option::ADD_ENFANT, '%env(MERCREDI_ADD_ENFANT)%');
    $parameters->set(Option::PAIEMENT, '%env(MERCREDI_PAIEMENT)%');
    $parameters->set(Option::PLAINE, '%env(MERCREDI_PLAINE)%');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services
        ->load('AcMarche\Mercredi\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);

    $services
        ->set(DirectoryNamer::class)
        ->public();

    $services
        ->instanceof(AfterUserRegistration::class)
        ->tag('app.user.after_registration');

    $services
        ->set(Register::class)
        ->arg('$secondaryFlows', tagged_iterator('app.user.after_registration'));

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Ldap::class)
            ->args(['@Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'])
            ->tag('ldap');
        $services
            ->set(Adapter::class)
            ->args(
                [
                    [
                        'host' => '%env(ACLDAP_URL)%',
                        'port' => 636,
                        'encryption' => 'ssl',
                        'options' => [
                            'protocol_version' => 3,
                            'referrals' => false,
                        ],
                    ],
                ],
            );

        $services
            ->set(LdapMercredi::class)
            ->arg('$adapter', service(Adapter::class))
            ->tag('ldap'); //necessary for new LdapBadge(LdapMercredi::class)
    }

    $services
        ->set(TokenManager::class)
        ->arg('$formLoginAuthenticator', service('security.authenticator.form_login.main'));
    /*  $services->set(PresenceConstraints::class)
          ->arg('$constraints', tagged_iterator('mercredi.presence_constraint'));*/
};
