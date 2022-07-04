<?php

use Symfony\Config\DoctrineConfig;
//use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine) {

  /*  $doctrine->dbal()
        ->connection('grh')
        ->url(env('DATABASE_GRH_URL')->resolve())
        ->charset('utf8mb4');*/

    $emMda = $doctrine->orm()->entityManager('default');
    $emMda->connection('default');
    $emMda->mapping('AcMarcheGrh')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Mercredi/src/Entity')
        ->prefix('AcMarche\Mercredi')
        ->alias('AcMarcheMercredi');
};
