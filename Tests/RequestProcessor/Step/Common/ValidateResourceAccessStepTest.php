<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\PersistStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateFormStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateResourceAccessStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);

        $step = new ValidateResourceAccessStep($accessManagerMock);
        $this->assertInstanceOf(ValidateResourceAccessStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);

        $step = new ValidateResourceAccessStep($accessManagerMock);
        $this->assertEquals([GetReflectionFromRequestStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessResource')->willReturn(true);

        $step = new ValidateResourceAccessStep($accessManagerMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'reflection' => new \ReflectionClass(MyModel::class)
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(\ReflectionClass::class, $response->getData()['reflection']);
    }

    public function testExecuteFailure()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessResource')->willReturn(false);

        $step = new ValidateResourceAccessStep($accessManagerMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'reflection' => new \ReflectionClass(MyModel::class)
        ]));

        $this->expectException(AccessDeniedHttpException::class);
        $step->execute();
    }


}
