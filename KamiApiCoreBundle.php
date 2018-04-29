<?php

namespace Kami\ApiCoreBundle;

use Kami\ApiCoreBundle\DependencyInjection\Compiler\StepsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KamiApiCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new StepsCompilerPass());
    }
}
