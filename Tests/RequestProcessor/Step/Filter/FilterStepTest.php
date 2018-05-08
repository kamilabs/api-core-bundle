<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\FetchEntityByIdStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Delete\DeleteStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Filter\FilterStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Filter\ValidateFilters;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FilterStepTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $step = new FilterStep($this->createMock(AccessManager::class));
        $this->assertInstanceOf(FilterStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $step = new FilterStep($this->createMock(AccessManager::class));
        $this->assertEquals([BuildSelectQueryStep::class, ValidateFilters::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->expects($this->any())->method('andWhere')->willReturn($queryBuilderMock);
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessProperty')->willReturn(true);
        $step = new FilterStep($accessManagerMock);

        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'filters' => [0 => ['type' => 'lk', 'property' => 'title', 'value' => 'a']],
            'query_builder' => $queryBuilderMock,
            'reflection' => new \ReflectionClass(MyModel::class)
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(QueryBuilder::class, $response->getData()['query_builder']);
    }

    public function testExecuteFailure()
    {
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessProperty')->will($this->throwException(new AccessDeniedHttpException()));
        $step = new FilterStep($accessManagerMock);

        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'filters' => [0 => ['type' => 'lk', 'property' => 'title', 'value' => 'a']],
            'query_builder' => $queryBuilderMock,
            'reflection' => new \ReflectionClass(MyModel::class)
        ]));

        $this->expectException(AccessDeniedHttpException::class);
        $step->execute();
    }

}
