<?php

namespace AcMarche\Mercredi\DependencyInjection;

use AcMarche\Mercredi\Contrat\Presence\PresenceConstraintInterface;
use AcMarche\Mercredi\Presence\Constraint\PresenceConstraints;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Population constraints
 * Class PresenceConstraintPass.
 */
class PresenceConstraintPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check finds out if there is an "PresenceConstraintInterface" definition or alias
        if (!$container->has(PresenceConstraintInterface::class)) {
            return;
        }

        // gets the definition with the "app.user_config_manager" ID or alias
        $definition = $container->findDefinition(PresenceConstraints::class);

        // find all service IDs with the mercredi.presence_constraint tag
        $taggedServices = $container->findTaggedServiceIds('mercredi.presence_constraint');

        foreach (array_keys($taggedServices) as $id) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addConstraint', [new Reference($id)]);
        }
    }
}
