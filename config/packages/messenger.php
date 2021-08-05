<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('messenger', [
        'failure_transport' => 'failed',
        'transports' => [
            'async' => [
                'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                'options' => [
                    'auto_setup' => false,
                    'use_notify' => true,
                    'check_delayed_interval' => 60000,
                ],
                'retry_strategy' => [
                    'max_retries' => 3,
                    'multiplier' => 2,
                ],
            ],
        ],
    ]);
};
