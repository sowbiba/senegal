<?php

namespace Api\SdkBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Registers event listeners to the available sdks.
 */
class SdkEventListenersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('event_dispatcher') && !$container->hasDefinition('debug.event_dispatcher')) {
            return;
        }

        $definition = $container->hasDefinition('event_dispatcher') ? $container->getDefinition('event_dispatcher') : $container->getDefinition('debug.event_dispatcher');
        $listeners  = $container->findTaggedServiceIds('api_sdk.event_listener');

        foreach ($listeners as $id => $instances) {
            foreach ($instances as $instance) {
                if (!isset($instance['event'])) {
                    throw new \InvalidArgumentException(sprintf(
                        'SDK event listener "%s" must specify the "event" attribute.',
                        $id
                    ));
                }

                $definition->addMethodCall(
                    'addListener',
                    array(sprintf("api_sdk_%s", $instance['event']), array(new Reference($id), $instance['event']))
                );
            }
        }
    }
}
