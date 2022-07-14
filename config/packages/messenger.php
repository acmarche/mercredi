<?php

use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Config\Framework\MessengerConfig;

return static function (MessengerConfig $messenger) {
    $messenger
        ->transport('async', [
            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
            'options' => [
                'auto_setup' => true,
                'use_notify' => true,
                //PostgreSQL’s
                'check_delayed_interval' => 60000,
            ],
            'retry_strategy' => [
                'max_retries' => 3,
                'multiplier' => 2,
            ],
        ]);
    //$messenger->routing(SendEmailMessage::class)->senders(['async']);
};
