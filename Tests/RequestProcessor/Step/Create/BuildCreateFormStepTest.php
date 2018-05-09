<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Create;

use Doctrine\Common\Annotations\Reader;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Create\BuildCreateFormStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class BuildCreateFormStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $accessManager = $this->createMock(AccessManager::class);
        $readerMock = $this->createMock(Reader::class);

        $step = new BuildCreateFormStep($formFactoryMock, $accessManager, $readerMock);

        $this->assertInstanceOf(BuildCreateFormStep::class, $step);
    }


    public function testExecute()
    {
        $formMock = $this->createMock(FormInterface::class);
        $formBuilderInterfaceMock = $this->createMock(FormBuilderInterface::class);
        $formBuilderInterfaceMock->expects($this->any())->method('add')->willReturn($formBuilderInterfaceMock);
        $formBuilderInterfaceMock->expects($this->any())->method('getForm')->willReturn($formMock);
        $request = new Request();

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->any())->method('createNamedBuilder')->willReturn($formBuilderInterfaceMock);
        $accessManager = $this->createMock(AccessManager::class);
        $readerMock = $this->createMock(Reader::class);

        $readerMock->expects($this->any())->method('getPropertyAnnotation')->willReturn([]);

        $accessManager->expects($this->any())->method('canCreateProperty')->willReturn(true);

        $step = new BuildCreateFormStep($formFactoryMock, $accessManager, $readerMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['reflection' => new \ReflectionClass(MyModel::class)]));
        $response = $step->execute();

        $this->assertInstanceOf(FormInterface::class, $response->getData()['form']);

    }

    public function testRequiresBefore()
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $accessManager = $this->createMock(AccessManager::class);
        $readerMock = $this->createMock(Reader::class);

        $step = new BuildCreateFormStep($formFactoryMock, $accessManager, $readerMock);
        $this->assertEquals([
            ValidateResourceAccessStep::class,
            GetEntityFromReflectionStep::class,
            GetReflectionFromRequestStep::class
        ], $step->requiresBefore());
    }
}