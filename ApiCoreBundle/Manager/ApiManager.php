<?php

namespace Kami\ApiCoreBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\ApiCoreEvents;
use Kami\ApiCoreBundle\Event\CrudEvent;
use Kami\ApiCoreBundle\Form\Factory;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApiManager
 *
 * @package Kami\ApiCoreBundle\Manager
 */
class ApiManager
{
    /**
     * @var Registry
     */
    private $doctrine;


    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var AccessManager
     */
    private $accessManager;

    /**
     * @var Factory
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int
     */
    private $perPage;

    /**
     * ApiManager constructor.
     *
     * @param Registry $doctrine
     * @param AccessManager $accessManager
     * @param Factory $formFactory
     * @param Serializer $serializer
     * @param EventDispatcherInterface $eventDispatcher
     * @param int $perPage
     */
    public function __construct(
        Registry $doctrine,
                                AccessManager $accessManager,
                                Factory $formFactory,
                                Serializer $serializer,
                                EventDispatcherInterface $eventDispatcher,
                                $perPage
    ) {
        $this->doctrine = $doctrine;
        $this->accessManager = $accessManager;
        $this->formFactory = $formFactory;
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
        $this->perPage = $perPage;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $entity = new \ReflectionClass($request->attributes->get('_entity'));
        $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_INDEX_REQUEST, new CrudEvent($entity));
        if (!$this->accessManager->canAccessResource($entity)) {
            throw new AccessDeniedHttpException();
        }


        $perPage = $request->query->getInt('per_page', $this->perPage);
        $currentPage = $request->query->getInt('page', 1);
        $data = $this->buildIndexQuery($entity, $perPage, $currentPage)->getResult();

        $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_INDEX_RESPONSE, new CrudEvent($data));

        return $this->createResponse($data, $request);
    }

    public function filter(Request $request)
    {
        $entity = new \ReflectionClass($request->attributes->get('_entity'));
        $builder = $this->doctrine->getRepository($entity->getName())->createQueryBuilder('e');

        $sort = $request->get('sort');
        if($sort) {
            $sort = lcfirst(implode('', array_map(function ($key) {
                return ucfirst($key);
            }, explode('_', $sort))));

            if(!$entity->hasProperty($sort)) {
                throw new BadRequestHttpException(sprintf('There is no such field %s', $sort));
            }
            $builder->orderBy(
                'e.'.$sort,
                in_array($request->get('order'), ['asc', 'desc']) ? $request->get('order') : 'desc'
            );
        }

        foreach($entity->getProperties() as $property) {
            $name = $property->getName();
            if($value = $request->get($name)) {
                $builder->andWhere($builder->expr()->orX(
                    $builder->expr()->like('e.'.$name, ':value')
                ));
                $builder->setParameter('value', '%'.$value.'%');
            }
        }

        $countBuilder = clone $builder;

        return $this->createResponse([
            'total' => $countBuilder->select('count(e.id)')->getQuery()->getSingleScalarResult(),
            'rows' => $builder
                ->setMaxResults($request->get('limit') ? $request->get('limit') : 20)
                ->setFirstResult($request->get('offset') ? $request->get('offset') : 0)
                ->getQuery()
                ->getResult()
        ], $request);

    }


    /**
     * @param Request $request
     * @return Response
     */
    public function getSingleResource(Request $request)
    {
        $entity = new \ReflectionClass($request->attributes->get('_entity'));
        $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_REQUEST, new CrudEvent($entity));
        if (!$this->accessManager->canAccessResource($entity)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->doctrine->getManager()->getRepository($entity->getName())->find($request->get('id'));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_RESPONSE, new CrudEvent($entity));
        return $this->createResponse($entity, $request);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createResource(Request $request)
    {
        $entityName = $request->attributes->get('_entity');
        $reflection = new \ReflectionClass($entityName);
        $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_CREATE, new CrudEvent($reflection));
        if (!$this->accessManager->canCreateResource($reflection)) {
            throw new AccessDeniedHttpException();
        }

        $entity = new $entityName;

        $form = $this->formFactory->getCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->persist($entity);
            $this->doctrine->getManager()->flush();

            $this->eventDispatcher->dispatch(ApiCoreEvents::RESOURCE_CREATED, new CrudEvent($entity));
            return $this->createResponse($entity, $request);
        }

        return $this->createResponse($form->getErrors(), $request, 400);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editResource(Request $request)
    {
        $entityName = $request->attributes->get('_entity');
        $reflection = new \ReflectionClass($entityName);
        if (!$this->accessManager->canEditResource($reflection)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->doctrine->getManager()->getRepository($entityName)->find($request->get('id'));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory->getEditForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->persist($entity);
            $this->doctrine->getManager()->flush();

            return $this->createResponse($entity, $request);
        }

        return $this->createResponse($form->getErrors(), $request, 400);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function deleteResource(Request $request)
    {
        $entityName = $request->attributes->get('_entity');
        $reflection = new \ReflectionClass($entityName);
        if (!$this->accessManager->canDeleteResource($reflection)) {
            throw new AccessDeniedHttpException();
        }

        $entity = $this->doctrine->getManager()
            ->getRepository($entityName)
            ->find($request->get('id'));

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();

        return $this->createResponse('', $request, 201);
    }

    /**
     * @param \ReflectionClass $entity
     * @param $perPage
     * @param $currentPage
     * @return Query
     */
    private function buildIndexQuery(\ReflectionClass $entity, $perPage, $currentPage)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->doctrine->getManager()
            ->createQueryBuilder()
            ->select('e')
            ->from($entity->getName(), 'e')
            ->setMaxResults($perPage)
            ->setFirstResult($perPage*($currentPage - 1))
        ;

        return $queryBuilder->getQuery();
    }

    /**
     * @param mixed $data
     * @param Request $request
     * @param int $status
     *
     * @return Response
     */
    private function createResponse($data, Request $request, $status = 200)
    {
        $format = $request->attributes->get('_format');

        return new Response(
            $this->serializer->serialize($data, $format),
            $status,
            ['Content-type' => $this->getContentTypeByFormat($format)]
        );
    }

    /**
     * @param string $format
     * @return string
     */
    private function getContentTypeByFormat($format)
    {
        switch ($format) {
            case 'json':
                return 'application/json';
                break;
            case 'xml':
                return 'application/xml';
                break;
            default:
                return 'text/plain';
                break;
        }
    }
}
