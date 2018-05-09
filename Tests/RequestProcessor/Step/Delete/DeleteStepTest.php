<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\FetchEntityByIdStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Delete\DeleteStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeleteStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $step = new DeleteStep($this->createMock(Registry::class));
        $this->assertInstanceOf(DeleteStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $step = new DeleteStep($this->createMock(Registry::class));
        $this->assertEquals([ValidateResourceAccessStep::class, FetchEntityByIdStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $entityManagerMock = $this->createMock(EntityManager::class);
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->any())->method('getManager')->willReturn($entityManagerMock);

        $step = new DeleteStep($doctrineMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'entity' => new MyModel()
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertEquals(null, $response->getData()['response_data']);
    }

    public function testExecuteFailure()
    {
        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->expects($this->any())->method('remove')->will($this->throwException(new BadRequestHttpException()));
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->any())->method('getManager')->willReturn($entityManagerMock);

        $step = new DeleteStep($doctrineMock);
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'entity' => new MyModel()
        ]));

        $this->expectException(BadRequestHttpException::class);
        $step->execute();
    }

}
