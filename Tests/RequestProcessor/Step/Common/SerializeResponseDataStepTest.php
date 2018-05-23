<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSerializerStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SerializeResponseDataStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class SerializeResponseDataStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $step = new SerializeResponseDataStep();
        $this->assertInstanceOf(SerializeResponseDataStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $step = new SerializeResponseDataStep();
        $this->assertEquals([BuildSerializerStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->any())->method('serialize')->willReturn('[]');

        $step = new SerializeResponseDataStep();
        $request = new Request();
        $request->attributes->set('_format', 'json');
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'serializer' => $serializerMock,
            'response_data' => []
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertEquals('[]', $response->getData()['response_data']);
    }
}
