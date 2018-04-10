<?php

namespace src\Kami\ApiCoreBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\Form\Factory;
use Kami\ApiCoreBundle\Manager\ApiManager;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiManagerTest extends WebTestCase
{
    // can be constructed with necessary params

    public function testCanBeConstructedWithNecessaryParams()
    {
        $apiManager = new ApiManager(
            $this->mock(Registry::class),
            $this->mock(AccessManager::class),
            $this->mock(Factory::class),
            $this->mock(Serializer::class),
            $this->mock(EventDispatcherInterface::class),
            10
        );
        $this->assertInstanceOf(ApiManager::class, $apiManager);
    }

    // Get Index

    public function testGetIndexMethod_notAccess()
    {
        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', false);
        $factoryMock = $this->mock(Factory::class);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(AccessDeniedHttpException::class);
        $apiManager->getIndex($requestMock);
    }
    public function testGetIndexMethod_Access()
    {
        $getQuery = $this->mock(AbstractQuery::class, 'getResult', 44444);

        $queryBuilderMock = $this->mock(QueryBuilder::class, 'getQuery', $getQuery);
        $queryBuilderMock->expects($this->any())
            ->method('select')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('from')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('setMaxResults')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('setFirstResult')
            ->willReturn($queryBuilderMock);

        $entityManagerMock = $this->mock(EntityManager::class, 'createQueryBuilder', $queryBuilderMock);
        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', true);
        $factoryMock = $this->mock(Factory::class);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class);
        $requestMock->attributes->expects($this->at(0))->method('get')->willReturn($entityName);
        $requestMock->attributes->expects($this->at(1))->method('get')->willReturn('json');

        $requestMock->query = $this->mock(ParameterBag::class);
        $requestMock->query->expects($this->at(0))->method('getInt')->willReturn(1);
        $requestMock->query->expects($this->at(1))->method('getInt')->willReturn(1);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);

        $response = $apiManager->getIndex($requestMock);

        $status = json_decode($response->getStatusCode(), true);
        $this->assertEquals(200, $status);
    }

    // Filter

    public function testFilterMethodWithoutSort()
    {
        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $getQuery = $this->mock(AbstractQuery::class, 'getSingleScalarResult', 1);
        $queryBuilderMock = $this->mock(QueryBuilder::class, 'getQuery', $getQuery);
        $queryBuilderMock->expects($this->once())
            ->method('select')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
            ->method('setMaxResults')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
            ->method('setFirstResult')
            ->willReturn($queryBuilderMock);
        $repositoryMock = $this->mock(EntityRepository::class, 'createQueryBuilder', $queryBuilderMock);
        $registryMock = $this->mock(Registry::class, 'getRepository', $repositoryMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', true);
        $factoryMock = $this->mock(Factory::class);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);

        $response = $apiManager->filter($requestMock);

        $status = json_decode($response->getStatusCode(), true);
        $this->assertEquals(200, $status);
    }
    public function testFilterMethodWithSortNotCurrentSortInUrl()
    {
        $requestMock = $this->mock(Request::class);
        $requestMock->expects($this->at(0))->method('get')->with('sort')->willReturn('ddddd');
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $getQuery = $this->mock(AbstractQuery::class, 'getSingleScalarResult', 1);
        $queryBuilderMock = $this->mock(QueryBuilder::class, 'getQuery', $getQuery);
        $queryBuilderMock->expects($this->any())
            ->method('select')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('setMaxResults')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('setFirstResult')
            ->willReturn($queryBuilderMock);
        $repositoryMock = $this->mock(EntityRepository::class, 'createQueryBuilder', $queryBuilderMock);
        $registryMock = $this->mock(Registry::class, 'getRepository', $repositoryMock);
        $accessManagerMock = $this->mock(AccessManager::class);
        $factoryMock = $this->mock(Factory::class);

        $this->expectException(BadRequestHttpException::class);
        $this->getApiManager($registryMock, $accessManagerMock, $factoryMock)->filter($requestMock, $accessManagerMock);
    }

// Get Single Resource

    public function testGetSingleResource_WithoutAccess()
    {
        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', false);
        $factoryMock = $this->mock(Factory::class);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName, '_entity');

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(AccessDeniedHttpException::class);
        $apiManager->getSingleResource($requestMock);
    }
    public function testGetSingleResourceWithoutEntity()
    {
        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName, '_entity');

        $repository = $this->mock(EntityRepository::class, 'find', null);
        $manager = $this->mock(EntityManager::class, 'getRepository', $repository);

        $registryMock = $this->mock(Registry::class, 'getManager', $manager);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', true);
        $factoryMock = $this->mock(Factory::class);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(NotFoundHttpException::class);
        $apiManager->getSingleResource($requestMock);
    }
    public function testGetSingleResourceAccess()
    {
        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';

        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);
        $requestMock->query = $this->mock(ParameterBag::class, 'getInt', 1);

        $repositoryMock = $this->mock(EntityRepository::class, 'find', 13);
        $entityManagerMock = $this->mock(EntityManager::class, 'getRepository', $repositoryMock);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', true);
        $factoryMock = $this->mock(Factory::class);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);

        $response = $apiManager->getSingleResource($requestMock);

        $status = json_decode($response->getStatusCode(), true);
        $this->assertEquals(200, $status);
    }

    // Create Resource

    public function testCreateResourceNotAccess()
    {
        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canAccessResource', false);
        $factoryMock = $this->mock(Factory::class);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(AccessDeniedHttpException::class);
        $apiManager->createResource($requestMock);
    }
    public function testCreateResourceFormNotSubmitted()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isSubmitted', false);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canCreateResource', true);
        $factoryMock = $this->mock(Factory::class, 'getCreateForm', $formInterfaceMock);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->createResource($requestMock);

        $this->assertEquals(400, $response->getStatusCode());
    }
    public function testCreateResourceFormNotValid()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isValid', false);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canCreateResource', true);
        $factoryMock = $this->mock(Factory::class, 'getCreateForm', $formInterfaceMock);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->createResource($requestMock);

        $this->assertEquals(400, $response->getStatusCode());
    }
    public function testCreateResourceFormSubmittedAndValid()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isValid', true);
        $formInterfaceMock->expects($this->any())->method('isSubmitted')->willReturn(true);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $entityManagerMock = $this->mock(EntityManager::class);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canCreateResource', true);
        $factoryMock = $this->mock(Factory::class, 'getCreateForm', $formInterfaceMock);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->createResource($requestMock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    // Edit Resource

    public function testEditResourceNotAccess()
    {
        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canEditResource', false);
        $factoryMock = $this->mock(Factory::class);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(AccessDeniedHttpException::class);
        $apiManager->editResource($requestMock);
    }
    public function testEditResourceNotEntityById()
    {
        $repositoryMock = $this->mock(EntityRepository::class, 'find', null);
        $entityManagerMock = $this->mock(EntityManager::class, 'getRepository', $repositoryMock);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canEditResource', true);
        $factoryMock = $this->mock(Factory::class);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(NotFoundHttpException::class);
        $apiManager->editResource($requestMock);
    }
    public function testEditResourceNotSubmittedForm()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isSubmitted', false);
        $repositoryMock = $this->mock(EntityRepository::class, 'find', 1);
        $entityManagerMock = $this->mock(EntityManager::class, 'getRepository', $repositoryMock);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canEditResource', true);
        $factoryMock = $this->mock(Factory::class, 'getEditForm', $formInterfaceMock);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->editResource($requestMock);

        $this->assertEquals(400, $response->getStatusCode());
    }
    public function testEditResourceNotValidForm()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isValid', false);
        $repositoryMock = $this->mock(EntityRepository::class, 'find', 1);
        $entityManagerMock = $this->mock(EntityManager::class, 'getRepository', $repositoryMock);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canEditResource', true);
        $factoryMock = $this->mock(Factory::class, 'getEditForm', $formInterfaceMock);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->editResource($requestMock);

        $this->assertEquals(400, $response->getStatusCode());
    }
    public function testEditResourceFormSubmittedAndValid()
    {
        $formInterfaceMock = $this->mock(FormInterface::class, 'isValid', true);
        $formInterfaceMock->expects($this->any())->method('isSubmitted')->willReturn(true);
        $repositoryMock = $this->mock(EntityRepository::class, 'find', 1);
        $entityManagerMock = $this->mock(EntityManager::class, 'getRepository', $repositoryMock);

        $registryMock = $this->mock(Registry::class, 'getManager', $entityManagerMock);
        $accessManagerMock = $this->mock(AccessManager::class, 'canEditResource', true);
        $factoryMock = $this->mock(Factory::class, 'getEditForm', $formInterfaceMock);


        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $response = $apiManager->editResource($requestMock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    // Delete Resource

    public function testDeleteResourceWithoutAccess()
    {
        $registryMock = $this->mock(Registry::class);
        $accessManagerMock = $this->mock(AccessManager::class, 'canDeleteResource', false);
        $factoryMock = $this->mock(Factory::class);

        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName, '_entity');

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(AccessDeniedHttpException::class);
        $apiManager->deleteResource($requestMock);
    }
    public function testDeleteResourceWithoutEntity()
    {
        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';
        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName, '_entity');

        $repository = $this->mock(EntityRepository::class, 'find', null);
        $manager = $this->mock(EntityManager::class, 'getRepository', $repository);

        $registryMock = $this->mock(Registry::class, 'getManager', $manager);
        $accessManagerMock = $this->mock(AccessManager::class, 'canDeleteResource', true);
        $factoryMock = $this->mock(Factory::class);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);
        $this->expectException(NotFoundHttpException::class);
        $apiManager->deleteResource($requestMock);
    }
    public function testDeleteResourceAccess()
    {
        $requestMock = $this->mock(Request::class);
        $entityName  = 'Kami\ApiCoreBundle\Tests\Entity\MyModel';

        $requestMock->attributes = $this->mock(ParameterBag::class, 'get', $entityName);
        $requestMock->query = $this->mock(ParameterBag::class, 'getInt', 1);

        $repository = $this->mock(EntityRepository::class, 'find', 13);
        $manager = $this->mock(EntityManager::class, 'getRepository', $repository);

        $registryMock = $this->mock(Registry::class, 'getManager', $manager);
        $accessManagerMock = $this->mock(AccessManager::class, 'canDeleteResource', true);
        $factoryMock = $this->mock(Factory::class);

        $apiManager = $this->getApiManager($registryMock, $accessManagerMock, $factoryMock);

        $response = $apiManager->deleteResource($requestMock);

        $status = json_decode($response->getStatusCode(), true);
        $this->assertEquals(201, $status);
    }



    private function mock($class, $expectedMethod = null, $willReturn = null, $methodParameter = null)
    {
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        if($expectedMethod){
            if($methodParameter){
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->with($methodParameter)
                    ->willReturn($willReturn);
            } else{
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->willReturn($willReturn);
            }
        }
        return $mock;
    }
    private function getApiManager($registryMock, $accessManagerMock, $factoryMock)
    {
        $serializerMock = $this->mock(Serializer::class);
        $eventDispatcherMock = $this->mock(EventDispatcherInterface::class);
        $perPage = 1;

        return new ApiManager($registryMock, $accessManagerMock, $factoryMock, $serializerMock, $eventDispatcherMock, $perPage);
    }
}