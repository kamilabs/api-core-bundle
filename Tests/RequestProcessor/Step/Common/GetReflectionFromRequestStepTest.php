<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetReflectionFromRequestStepTest extends TestCase
{

    public function testExecute()
    {
        $request = new Request();
        $request->attributes->set('_entity', Entity::class);
        $step = new GetReflectionFromRequestStep();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, []));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(\ReflectionClass::class, $response->getData()['reflection']);
    }

    public function testExecuteFailure()
    {
        $request = new Request();
        $request->attributes->set('_entity', 'Not\Existing\Class');
        $step = new GetReflectionFromRequestStep();
        $step->setPreviousResponse(new ProcessorResponse($request, []));
        $step->setRequest($request);
        $this->expectException(NotFoundHttpException::class);
        $step->execute();
    }

    public function testRequiresBefore()
    {
        $step = new GetReflectionFromRequestStep();
        $this->assertEquals([], $step->requiresBefore());
    }
}
