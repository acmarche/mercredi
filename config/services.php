<?php

use AcMarche\Mercredi\Jour\Tarification\Form\TarificationFormGeneratorInterface;
use AcMarche\Mercredi\Jour\Tarification\Form\TarificationHottonFormGenerator;
use AcMarche\Mercredi\Namer\DirectoryNamer;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Plaine\Calculator\PlaineCalculatorInterface;
use AcMarche\Mercredi\Plaine\Calculator\PlaineHottonCalculator;
use AcMarche\Mercredi\Presence\Calculator\PrenceHottonCalculator;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Security\Ldap\LdapMercredi;
use AcMarche\Mercredi\ServiceIterator\AfterUserRegistration;
use AcMarche\Mercredi\ServiceIterator\Register;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\LdapInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::EMAIL_SENDER, '%env(MERCREDI_FROM)%');
    $parameters->set(Option::REGISTER, (bool)'%env(MERCREDI_REGISTER)%');
    $parameters->set(Option::ACCUEIL_PRIX, '%env(MERCREDI_ACCUEIL_PRIX)%');
    $parameters->set(Option::PRESENCE_PRIX1, '%env(MERCREDI_PRESENCE_PRIX1)%');
    $parameters->set(Option::PRESENCE_PRIX2, '%env(MERCREDI_PRESENCE_PRIX2)%');
    $parameters->set(Option::PRESENCE_PRIX3, '%env(MERCREDI_PRESENCE_PRIX3)%');
    $parameters->set(Option::PLAINE_PRIX1, '%env(MERCREDI_PLAINE_PRIX1)%');
    $parameters->set(Option::PLAINE_PRIX2, '%env(MERCREDI_PLAINE_PRIX2)%');
    $parameters->set(Option::PLAINE_PRIX3, '%env(MERCREDI_PLAINE_PRIX3)%');
    $parameters->set(Option::PRESENCE_DEADLINE_DAYS, '%env(MERCREDI_PRESENCE_DEADLINE_DAYS)%');
    $parameters->set(Option::PEDAGOGIQUE_DEADLINE_DAYS, '%env(MERCREDI_PEDAGOGIQUE_DEADLINE_DAYS)%');
    $parameters->set(Option::LDAP_DN, '%env(ACLDAP_DN)%');
    $parameters->set(Option::LDAP_USER, '%env(ACLDAP_USER)%');
    $parameters->set(Option::LDAP_PASSWORD, '%env(ACLDAP_PASSWORD)%');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\Mercredi\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);

    $services->set(DirectoryNamer::class)
        ->public();

    $services->alias(TarificationFormGeneratorInterface::class, TarificationHottonFormGenerator::class);

    $services->alias(PresenceCalculatorInterface::class, PrenceHottonCalculator::class);

    $services->alias(PlaineCalculatorInterface::class, PlaineHottonCalculator::class);

    $services->alias(LoaderInterface::class, 'fidry_alice_data_fixtures.loader.doctrine');

    $services->instanceof(AfterUserRegistration::class)
        ->tag('app.user.after_registration');

    $services->set(Register::class)
        ->arg('$secondaryFlows', tagged_iterator('app.user.after_registration'));

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Symfony\Component\Ldap\Ldap::class)
            ->args(['@Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'])
            ->tag('ldap');
        $services->set(Adapter::class)->args(
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
            ]
        );

        $services->set(LdapMercredi::class)
            ->arg('$adapter', service('Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'))
            ->tag('ldap'); //necessary for new LdapBadge(LdapMercredi::class)
    }

    /*  $services->set(PresenceConstraints::class)
          ->arg('$constraints', tagged_iterator('mercredi.presence_constraint'));*/
};
