<?php

namespace AcMarche\Mercredi;

use AcMarche\Mercredi\DependencyInjection\PresenceConstraintPass;
use function dirname;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AcMarcheMercrediBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PresenceConstraintPass());
    }
}
