<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SerializeResponseDataStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class SerializeResponseDataStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $serializerMock = $this->createMock(Serializer::class);

        $step = new SerializeResponseDataStep($serializerMock);
        $this->assertInstanceOf(SerializeResponseDataStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $serializerMock = $this->createMock(Serializer::class);

        $step = new SerializeResponseDataStep($serializerMock);
        $this->assertEquals([], $step->requiresBefore());
    }

    public function testExecute()
    {
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->any())->method('serialize')->willReturn($serializerMock);

        $step = new SerializeResponseDataStep($serializerMock);
        $request = new Request();
        $request->attributes->set('_format', 'json');
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'response_data' => 1
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(Serializer::class, $response->getData()['response_data']);
    }
}
