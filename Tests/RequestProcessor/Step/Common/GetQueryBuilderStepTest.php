<?php


namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetQueryBuilderStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GetQueryBuilderStepTest extends TestCase
{
    public function testCanBeCreated()
    {
        $step = new GetQueryBuilderStep($this->createMock(Registry::class));
        $this->assertInstanceOf(GetQueryBuilderStep::class, $step);
    }

    public function testExecute()
    {
        $request = new Request();
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->expects($this->at(0))->method('createQueryBuilder')->willReturn($queryBuilderMock);
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->at(0))->method('getManager')->willReturn($entityManagerMock);

        $step = new GetQueryBuilderStep($doctrineMock);
        $response = $step->execute($request);
        $this->assertInstanceOf(ArtifactCollection::class, $response);

    }

    public function testGetRequiredArtifacts()
    {
        $step = new GetQueryBuilderStep($this->createMock(Registry::class));

        $this->assertEquals(['access_granted'], $step->getRequiredArtifacts());
    }
}