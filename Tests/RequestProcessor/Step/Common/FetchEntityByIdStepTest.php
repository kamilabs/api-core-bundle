<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Repository\RepositoryFactory;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\FetchEntityByIdStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use Kami\ApiCoreBundle\Tests\Repository\TestRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FetchEntityByIdStepTest extends TestCase
{

    public function testExecute()
    {
        $repositoryMock = $this->createMock(TestRepository::class);
        $repositoryMock->expects($this->at(0))->method('find')->willReturn(new MyModel());
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->at(0))->method('getRepository')->willReturn($repositoryMock);
        $request = new Request();
        $request->attributes->set('id', 1);
        $step = new FetchEntityByIdStep($doctrineMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['reflection' => new \ReflectionClass(MyModel::class)]));
        $response = $step->execute();
        
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(MyModel::class, $response->getData()['entity']);
    }

    public function testExecuteFailure()
    {
        $repositoryMock = $this->createMock(TestRepository::class);
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->at(0))->method('getRepository')->willReturn($repositoryMock);
        $request = new Request();
        $step = new FetchEntityByIdStep($doctrineMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['reflection' => new \ReflectionClass(MyModel::class)]));

        $this->expectException(NotFoundHttpException::class);
        $step->execute();
    }

    public function testRequiresBefore()
    {
        $step = new FetchEntityByIdStep($this->createMock(Registry::class));
        $this->assertEquals([GetReflectionFromRequestStep::class], $step->requiresBefore());
    }
}
