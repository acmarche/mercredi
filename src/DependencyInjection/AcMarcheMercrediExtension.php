<?php

namespace AcMarche\Mercredi\DependencyInjection;

use AcMarche\Mercredi\Presence\Constraint\PresenceConstraintInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
final class AcMarcheMercrediExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));
        $phpFileLoader->load('services.php');

        //auto tag PresenceConstraintInterface
        $containerBuilder->registerForAutoconfiguration(PresenceConstraintInterface::class)
            ->addTag('mercredi.presence_constraint');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        // get all bundles
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach (array_keys($containerBuilder->getExtensions()) as $name) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfig($containerBuilder, 'doctrine');

                        break;
                    case 'twig':
                        $this->loadConfig($containerBuilder, 'twig');

                        break;
                    case 'liip_imagine':
                        $this->loadConfig($containerBuilder, 'liip_imagine');

                        break;
                    case 'framework':
                        $this->loadConfig($containerBuilder, 'security');

                        break;
                    case 'vich_uploader':
                        $this->loadConfig($containerBuilder, 'vich_uploader');

                        break;
                    case 'messenger':
                        $this->loadConfig($containerBuilder, 'messenger');

                        break;
                    case 'api_platform':
                        //$this->loadConfig($container, 'api_platform');
                        break;
                }
            }
        }
    }

    protected function loadConfig(ContainerBuilder $containerBuilder, string $name): void
    {
        //https://symfony.com/doc/current/bundles/prepend_extension.html
        //$containerBuilder->prependExtensionConfig($name, $config);
        $configs = $this->loadConfigFile($containerBuilder);

        $configs->load($name.'.php');
    }

    protected function loadConfigFile(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages/')
        );
    }
}
