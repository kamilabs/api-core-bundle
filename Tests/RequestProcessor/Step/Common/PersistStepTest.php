<?php


namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\PersistStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateFormStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PersistStepTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $this->assertInstanceOf(PersistStep::class, new PersistStep($this->createMock(Registry::class)));
    }

    public function testExecute()
    {
        $request = new Request();
        $doctrineMock = $this->createMock(Registry::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $doctrineMock->expects($this->at(0))->method('getManager')->willReturn($entityManagerMock);

        $step = new PersistStep($doctrineMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['entity' => new MyModel()]));
        $response = $step->execute();
        $this->assertInstanceOf(MyModel::class, $response->getData()['response_data']);
    }

    public function testExecuteFailure()
    {
        $request = new Request();
        $doctrineMock = $this->createMock(Registry::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->expects($this->any())->method('persist')->will($this->throwException(new BadRequestHttpException()));
        $doctrineMock->expects($this->at(0))->method('getManager')->willReturn($entityManagerMock);

        $step = new PersistStep($doctrineMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['entity' => new MyModel()]));

        $this->expectException(BadRequestHttpException::class);
        $step->execute();
    }

    public function testRequiresBefore()
    {
        $step = new PersistStep($this->createMock(Registry::class));

        $this->assertEquals([ValidateFormStep::class], $step->requiresBefore());
    }
}