<?php


namespace Kami\ApiCoreBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class StepsCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tags = $container->findTaggedServiceIds('kami_api_core.strategy_step');
        if (count($tags) > 0 && $container->hasDefinition('kami.api_core.strategy_factory')) {
            $requestProcessor = $container->getDefinition('kami.api_core.strategy_factory');
            foreach ($tags as $id => $tag) {
                $requestProcessor->addMethodCall('addStep', array($tag[0]['shortcut'], new Reference($id)));
            }
        }
    }

}