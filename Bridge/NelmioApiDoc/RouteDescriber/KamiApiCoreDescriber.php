<?php

namespace Kami\ApiCoreBundle\Bridge\NelmioApiDoc\RouteDescriber;


use EXSyst\Component\Swagger\Swagger;
use Kami\ApiCoreBundle\Controller\ApiController;
use Kami\ApiCoreBundle\Documentator\Stenographer;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use Symfony\Component\Routing\Route;


class KamiApiCoreDescriber implements RouteDescriberInterface
{

    private $stenographer;

    public function __construct(Stenographer $stenographer)
    {
        $this->stengographer = $stenographer;
    }

    public function describe(Swagger $api, Route $route, \ReflectionMethod $reflectionMethod)
    {
        if (ApiController::class === $reflectionMethod->getDeclaringClass()) {
            return $this->stenographer->getStenography();
        }
    }

}