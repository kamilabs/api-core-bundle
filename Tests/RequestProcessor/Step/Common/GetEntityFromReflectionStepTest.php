<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetEntityFromReflectionStepTest extends TestCase
{

    public function testExecute()
    {
        $step = new GetEntityFromReflectionStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['reflection' => new \ReflectionClass(MyModel::class)]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(MyModel::class, $response->getData()['entity']);
    }
    
    public function testRequiresBefore()
    {
        $step = new GetEntityFromReflectionStep();
        $this->assertEquals([GetReflectionFromRequestStep::class], $step->requiresBefore());
    }
}
