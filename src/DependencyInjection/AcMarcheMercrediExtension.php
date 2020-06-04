<?php

namespace AcMarche\Mercredi\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AcMarcheMercrediExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfig($container, 'doctrine');
                        break;
                    case 'twig':
                        $this->loadConfig($container, 'twig');
                        break;
                    case 'liip_imagine':
                        $this->loadConfig($container, 'liip_imagine');
                        break;
                    case 'framework':
                        $this->loadConfig($container, 'security');
                        break;
                    case 'vich_uploader':
                        $this->loadConfig($container, 'vich_uploader');
                        break;
                    case 'api_platform':
                        //$this->loadConfig($container, 'api_platform');
                        break;
                }
            }
        }
    }

    protected function loadConfig(ContainerBuilder $container, string $name)
    {
        $configs = $this->loadYamlFile($container);

        $configs->load($name.'.yaml');
    }

    protected function loadYamlFile(ContainerBuilder $container): Loader\YamlFileLoader
    {
        return new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config/packages/')
        );
    }
}
