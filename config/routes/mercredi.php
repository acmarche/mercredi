<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../../src/AcMarche/Mercredi/src/Controller/Front/');

    $routingConfigurator->import('../../src/AcMarche/Mercredi/src/Controller/Admin/')
        ->prefix('admin/');

    $routingConfigurator->import('../../src/AcMarche/Mercredi/src/Controller/Parent/')
        ->prefix('parent/');
};
