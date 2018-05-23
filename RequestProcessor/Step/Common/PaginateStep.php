<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kami\ApiCoreBundle\Model\Pageable;
use Kami\ApiCoreBundle\Model\PageRequest;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PaginateStep
 * @package Kami\ApiCoreBundle\RequestProcessor\Step\Common
 */
class PaginateStep extends AbstractStep
{
    protected $maxPerPage;

    protected $perPage;

    /**
     * @param int $perPage
     * @param int $maxPerPage
     */
    public function __construct($perPage, $maxPerPage)
    {
        $this->perPage = $perPage;
        $this->maxPerPage = $maxPerPage;
    }

    public function execute()
    {
        $perPage = $this->request->query->getInt('per_page', $this->perPage);

        if ($perPage > $this->maxPerPage) {
            throw new BadRequestHttpException('Max per page parameter is greater than allowed');
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        $currentPage = $this->request->query->getInt('page', 1);
        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($currentPage);

        if ($currentPage < 1 || $currentPage > $paginator->getNbPages()) {
            throw new NotFoundHttpException();
        }
        return $this->createResponse(['response_data' =>
            new Pageable(
                iterator_to_array($paginator->getIterator()),
                $paginator->getNbResults(),
                new PageRequest($currentPage, $paginator->getNbPages())
            )
        ]);
    }

    public function requiresBefore()
    {
        return [BuildSelectQueryStep::class];
    }

}