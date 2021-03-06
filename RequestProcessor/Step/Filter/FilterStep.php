<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Filter;


use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FilterStep extends AbstractStep
{
    protected $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function execute(Request $request) : ArtifactCollection
    {
        /** @var array $filters */
        $filters = $this->getArtifact('filters');

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getArtifact('query_builder');
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getArtifact('reflection');

        foreach ($filters as $filter) {
            $property = $reflection->getProperty($filter['property']);
            if (!$this->accessManager->canAccessProperty($property)) {
                throw new AccessDeniedHttpException();
            }
            call_user_func([$this, sprintf('apply%sFilter', $filter['type'])], $filter, $queryBuilder);
        }

        return new ArtifactCollection();
    }

    public function applyEqFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf('e.%s = :%s_value', $filter['property'], $filter['property']))
            ->setParameter(sprintf('%s_value', $filter['property']), $filter['value']);
    }

    public function applyGtFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf('e.%s > :%s_value', $filter['property'], $filter['property']))
            ->setParameter(sprintf('%s_value', $filter['property']), $filter['value']);
    }

    public function applyLtFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf('e.%s < :%s_value', $filter['property'], $filter['property']))
            ->setParameter(sprintf('%s_value', $filter['property']), $filter['value']);
    }

    public function applyInFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf('e.%s IN (:%s_value)', $filter['property'], $filter['property']))
            ->setParameter(sprintf('%s_value', $filter['property']), $filter['value']);
    }

    public function applyBwFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(
                sprintf(
                    'e.%s BETWEEN :%s_min_value AND :%s_max_value',
                    $filter['property'],
                    $filter['property'],
                    $filter['property']
                )
            )
            ->setParameter(sprintf('%s_min_value', $filter['property']), $filter['min'])
            ->setParameter(sprintf('%s_max_value', $filter['property']), $filter['max']);
        ;
    }

    public function applyLkFilter($filter, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere(sprintf('e.%s LIKE :%s_value', $filter['property'], $filter['property']))
            ->setParameter(sprintf('%s_value', $filter['property']), '%'.$filter['value'].'%');
    }

    public function getRequiredArtifacts() : array
    {
        return ['filters', 'query_builder', 'access_granted', 'reflection', 'select_query_built'];
    }
}