<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kami\ApiCoreBundle\Model\Pageable;
use Kami\ApiCoreBundle\Model\PageRequest;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
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

        $queryBuilder->setFirstResult($perPage*($currentPage - 1));
        $queryBuilder->setMaxResults($this->perPage);

        $total = (new Paginator($queryBuilder))->count();

        $totalPages = ceil($total/$this->perPage);

        if ($currentPage < 1 || $currentPage > $totalPages) {
            throw new NotFoundHttpException();
        }


        return $this->createResponse(['response_data' =>
            new Pageable(
                $queryBuilder->getQuery()->getArrayResult(),
                $total,
                new PageRequest($currentPage, $totalPages)
            )
        ]);
    }

    public function requiresBefore()
    {
        return [BuildSelectQueryStep::class];
    }

}