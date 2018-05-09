<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\Item\AddWhereStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Item\SetSelectedDataStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class SetSelectedDataStepTest extends TestCase
{
    public function testRequiresBefore()
    {
        $step = new SetSelectedDataStep();
        $this->assertEquals([AddWhereStep::class], $step->requiresBefore());
    }

//    public function testExecute()
//    {
////        todo cann't create mock oj final Query::class
//
//        $queryMock = $this->createMock(Query::class);
//        $queryMock->expects($this->any())->method('getSingleResult')->willReturn(new MyModel());
//        $queryBuilderMock = $this->createMock(QueryBuilder::class);
//        $queryBuilderMock->expects($this->any())->method('getQuery')->willReturn($queryMock);
//
//        $step = new SetSelectedDataStep();
//        $request = new Request();
//        $step->setRequest($request);
//        $step->setPreviousResponse(new ProcessorResponse($request, ['query_builder' => $queryBuilderMock]));
//
//        $response = $step->execute();
//        $this->assertInstanceOf(ProcessorResponse::class, $response);
//        $this->assertInstanceOf(MyModel::class, $response->getData()['response_data']);
//    }
}
