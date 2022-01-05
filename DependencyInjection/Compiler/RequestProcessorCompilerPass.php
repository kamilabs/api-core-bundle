<?php


namespace Kami\ApiCoreBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RequestProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $tags = $container->findTaggedServiceIds('kami_api_core.request_processor');
        if (count($tags) > 0 && $container->hasDefinition('kami.api_core.processor_registry')) {
            $processorRegistry = $container->getDefinition('kami.api_core.processor_registry');
            foreach ($tags as $id => $tag) {
                $processorRegistry->addMethodCall('addProcessor', [ $id, new Reference($id) ]);
            }
        }
    }
}