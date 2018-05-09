<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\PersistStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\TrimResponseStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class TrimResponseStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);

        $step = new TrimResponseStep($accessManagerMock);
        $this->assertInstanceOf(TrimResponseStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);

        $step = new TrimResponseStep($accessManagerMock);
        $this->assertEquals([PersistStep::class], $step->requiresBefore());
    }

    public function testExecuteNoAccess()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessProperty')->willReturn(false);

        $step = new TrimResponseStep($accessManagerMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'reflection' => new \ReflectionClass(MyModel::class),
            'response_data' => new MyModel()
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertEquals([], $response->getData()['response_data']);
    }

    public function testExecute()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessProperty')->willReturn(true);

        $step = new TrimResponseStep($accessManagerMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'reflection' => new \ReflectionClass(MyModel::class),
            'response_data' => new MyModel()
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertArrayHasKey('id', $response->getData()['response_data']);
        $this->assertArrayHasKey('title', $response->getData()['response_data']);
    }
}
