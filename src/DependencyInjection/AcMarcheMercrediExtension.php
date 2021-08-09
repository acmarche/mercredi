<?php

namespace AcMarche\Mercredi\DependencyInjection;

use AcMarche\Mercredi\Presence\Constraint\PresenceConstraintInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
final class AcMarcheMercrediExtension extends Extension implements PrependExtensionInterface
{
    private PhpFileLoader $loader;

    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $this->loader->load('services.php');
        // $phpFileLoader->load('packages/messenger.php');

        $resolver = new LoaderResolver([
            $this->loader,
            new ClosureLoader($containerBuilder),
        ]);

        $delegatingLoader = new DelegatingLoader($resolver);
        //  $delegatingLoader->load('packages/messenger.php');
        // $delegatingLoader->import('packages/messenger.php');

        //auto tag PresenceConstraintInterface
        $containerBuilder->registerForAutoconfiguration(PresenceConstraintInterface::class)
            ->addTag('mercredi.presence_constraint');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $this->loader = $this->initPhpFilerLoader($containerBuilder);

        foreach (array_keys($containerBuilder->getExtensions()) as $name) {
            switch ($name) {
                case 'doctrine':
                    $this->loadConfig('doctrine');

                    break;
                case 'twig':
                    $this->loadConfig('twig');

                    break;
                case 'liip_imagine':
                    $this->loadConfig('liip_imagine');

                    break;
                case 'framework':
                    $this->loadConfig('security');

                    break;
                case 'vich_uploader':
                    $this->loadConfig('vich_uploader');

                    break;
            }
        }
    }

    protected function loadConfig(string $name): void
    {
        $this->loader->load('packages/'.$name.'.php');
    }

    protected function initPhpFilerLoader(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/')
        );
    }
}
