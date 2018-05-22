<?php


namespace Kami\ApiCoreBundle\Tests\RequestProcessor;

use Kami\ApiCoreBundle\RequestProcessor\ProcessingException;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProcessorResponseTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $requestMock = $this->createMock(Request::class);
        $processorResponse = new ProcessorResponse($requestMock, []);

        $this->assertInstanceOf(ProcessorResponse::class, $processorResponse);
    }

    public function testRequestReadyToSetAsHttp()
    {
        $request = new Request();
        $request->attributes->set('_format', 'json');
        $processorResponse = new ProcessorResponse($request, ['response_data'=>'data'], true);

        $this->assertInstanceOf(Response::class, $response = $processorResponse->toHttpResponse());
    }

    public function testRequestNotReadyToSetAsHttp()
    {
        $request = new Request();
        $processorResponse = new ProcessorResponse($request, []);

        $this->expectException(ProcessingException::class);

        $processorResponse->toHttpResponse();
    }
}