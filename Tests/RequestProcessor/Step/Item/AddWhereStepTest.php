<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Item\AddWhereStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class AddWhereStepTest extends TestCase
{
    public function testRequiresBefore()
    {
        $step = new AddWhereStep();
        $this->assertEquals([BuildSelectQueryStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->expects($this->any())->method('where')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())->method('setParameter')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())->method('setMaxResults')->willReturn($queryBuilderMock);
        $step = new AddWhereStep();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, ['query_builder' => $queryBuilderMock]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(QueryBuilder::class, $response->getData()['query_builder']);
    }
}
