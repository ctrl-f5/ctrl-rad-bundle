<?php

namespace Ctrl\RadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EntityServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('ctrl_rad.entity_service');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            if (!$definition->getArguments()) {
                $definition->setArguments(array(
                    new Reference('doctrine.orm.entity_manager')
                ));
            }
        }
    }
}