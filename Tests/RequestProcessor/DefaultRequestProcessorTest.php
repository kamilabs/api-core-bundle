<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\DefaultRequestProcessor;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetQueryBuilderStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SerializeResponseDataStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SortStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\StepInterface;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use const true;

class DefaultRequestProcessorTest extends TestCase
{

    public function testExecuteStrategy()
    {
        $accessManagerMock = $this->createMock(AccessManager::class);
        $accessManagerMock->expects($this->any())->method('canAccessResource')->willReturn(true);
        $accessManagerMock->expects($this->any())->method('canAccessProperty')->willReturn(true);
        $registryMock = $this->createMock(Registry::class);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $registryMock->expects($this->any())->method('getManager')->willReturn($entityManagerMock);
        $entityManagerMock->expects($this->any())->method('createQueryBuilder')->willReturn($queryBuilderMock);
        $annotationReaderMock = $this->createMock(Reader::class);
        $serializerMock = $this->createMock(Serializer::class);

        $availableSteps = [
            'get_reflection_from_request' => new GetReflectionFromRequestStep(),
            'validate_resource_access' => new ValidateResourceAccessStep($accessManagerMock),
            'get_query_builder' => new GetQueryBuilderStep($registryMock),
            'build_select_query' => new BuildSelectQueryStep($accessManagerMock, $annotationReaderMock),
            'sort' => new SortStep($accessManagerMock),
            'paginate' => new SortStep($accessManagerMock),
            'serialize_response_data' =>new SerializeResponseDataStep($serializerMock)
        ];
        $strategy = [
            'get_reflection_from_request',
            'validate_resource_access',
            'get_query_builder',
            'build_select_query',
            'sort',
            'paginate',
            'serialize_response_data'
        ];

        $request = new Request();
        $request->attributes->set('_entity', Entity::class);
        $request->attributes->set('_sort_direction', 'asc');
        $request->attributes->set('sort', 'id');

        $defaultRequestProcessor = new DefaultRequestProcessor();
        foreach ($availableSteps as $index => $availableStep) {
            $defaultRequestProcessor->addStep($index, $availableStep);
        }

        $this->assertInstanceOf(Response::class, $defaultRequestProcessor->executeStrategy($strategy, $request));
    }
}