<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Common\HandleRequestStep;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HandleRequestStepTest extends TestCase
{

    public function testExecute()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isSubmitted')->willReturn(true);
        $step = new HandleRequestStep();
        $request = new Request();
        $step->setArtifacts(new ArtifactCollection([
            new Artifact('form', $formMock),
            new Artifact('access_granted', true)
        ]));
        $response = $step->execute($request);
        $this->assertInstanceOf(ArtifactCollection::class, $response);
        $this->assertTrue($response->get('handled_request')->getValue());
    }

    public function testExecuteFailure()
    {
        $formMock = $this->createMock(Form::class);
        $formMock->expects($this->any())->method('isSubmitted')->willReturn(false);
        $step = new HandleRequestStep();
        $step->setArtifacts(new ArtifactCollection([
            new Artifact('form', $formMock),
            new Artifact('access_granted', true)
        ]));
        $request = new Request();
        $this->expectException(BadRequestHttpException::class);
        $step->execute($request);
    }
    
    public function testGetRequiredArtifacts()
    {
        $step = new HandleRequestStep();
        $this->assertEquals(['form', 'access_granted'], $step->getRequiredArtifacts());
    }
}
