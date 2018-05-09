<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\HandleRequestStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HandleRequestStepTest extends TestCase
{

    public function testExecute()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isSubmitted')->willReturn(true);
        $step = new HandleRequestStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['form' => $formMock]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(Form::class, $response->getData()['form']);
    }

    public function testExecuteFailure()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isSubmitted')->willReturn(false);
        $step = new HandleRequestStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['form' => $formMock]));

        $this->expectException(BadRequestHttpException::class);
        $step->execute();
    }
    
    public function testRequiresBefore()
    {
        $step = new HandleRequestStep();
        $this->assertEquals(['generic_build_form_step'], $step->requiresBefore());
    }
}
