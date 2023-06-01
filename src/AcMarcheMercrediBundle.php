<?php

namespace AcMarche\Mercredi;

use AcMarche\Mercredi\Contrat\Presence\PresenceConstraintInterface;
use AcMarche\Mercredi\DependencyInjection\PresenceConstraintPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AcMarcheMercrediBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
        //auto tag PresenceConstraintInterface
        $builder->registerForAutoconfiguration(PresenceConstraintInterface::class)
            ->addTag('mercredi.presence_constraint');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/doctrine.php');
        $container->import('../config/packages/framework.php');
        $container->import('../config/packages/liip_imagine.php');
        $container->import('../config/packages/messenger.php');
        $container->import('../config/packages/rate_limiter.php');
        $container->import('../config/packages/security.php');
        $container->import('../config/packages/twig.php');
        $container->import('../config/packages/vich_uploader.php');
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PresenceConstraintPass());
    }
}
