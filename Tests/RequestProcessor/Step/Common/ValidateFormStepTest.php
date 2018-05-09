<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Common\HandleRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateFormStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class ValidateFormStepTest extends TestCase
{

    public function testRequiresBefore()
    {
        $step = new ValidateFormStep();
        $this->assertEquals([HandleRequestStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isValid')->willReturn(true);
        $step = new ValidateFormStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'form' => $formMock
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);

        $this->assertEquals(1, count($response->getData()));
    }

    public function testExecuteFailure()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isValid')->willReturn(false);
        $step = new ValidateFormStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'form' => $formMock
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);

        $this->assertEquals(400, $response->getStatus());
        $this->assertArrayHasKey('response_data', $response->getData());
    }
}
