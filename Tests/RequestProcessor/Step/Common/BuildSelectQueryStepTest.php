<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetQueryBuilderStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class BuildSelectQueryStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $reader = $this->createMock(Reader::class);

        $step = new BuildSelectQueryStep($accessManager, $reader);
        $this->assertInstanceOf(BuildSelectQueryStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $reader = $this->createMock(Reader::class);

        $step = new BuildSelectQueryStep($accessManager, $reader);
        $this->assertEquals([GetQueryBuilderStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {
        $readerMock = $this->createMock(Reader::class);
        $accessManagerMock = $this->createMock(AccessManager::class);
        $request = new Request();
        $step = new BuildSelectQueryStep($accessManagerMock, $readerMock);
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, [
            'reflection' => new \ReflectionClass(MyModel::class),
            'query_builder' => $this->createMock(QueryBuilder::class)
        ]));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertInstanceOf(QueryBuilder::class, $response->getData()['query_builder']);
    }
}
