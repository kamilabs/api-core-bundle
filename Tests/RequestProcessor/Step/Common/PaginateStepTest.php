<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\HandleRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\PaginateStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaginateStepTest extends TestCase
{

    //todo paginator tests
//    public function testExecute()
//    {
//        $paginatorMock = $this->createMock(Paginator::class);
//        $paginatorMock->expects($this->any())->method('__construct')->willReturn($paginatorMock);
//        $paginatorMock->expects($this->any())->method('count')->willReturn(10);
//
//        $queryBuilderMock = $this->createMock(QueryBuilder::class);
//        $queryBuilderMock->expects($this->any())->method('getQuery')->willReturn($queryMock);
//
//        $step = new PaginateStep(10);
//        $request = new Request();
//        $step->setRequest($request);
//        $step->setPreviousResponse(new ProcessorResponse($request, ['query_builder' => $queryBuilderMock]));
//
//        $response = $step->execute();
//        $this->assertInstanceOf(ProcessorResponse::class, $response);
//        $this->assertInstanceOf(Form::class, $response->getData()['form']);
//    }

//    public function testExecuteFailure()
//    {
//        $formMock = $this->createMock(Form::class);
//        $formMock->expects($this->any())->method('isSubmitted')->willReturn(false);
//        $step = new HandleRequestStep();
//        $request = new Request();
//        $step->setRequest($request);
//        $step->setPreviousResponse(new ProcessorResponse($request, ['form' => $formMock]));
//
//        $this->expectException(BadRequestHttpException::class);
//        $response = $step->execute();
//    }
//    
    public function testRequiresBefore()
    {
        $step = new PaginateStep(10, 100);
        $this->assertEquals([BuildSelectQueryStep::class], $step->requiresBefore());
    }
}
