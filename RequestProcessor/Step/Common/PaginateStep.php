<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @param int $maxPerPage
     */
    public function __construct($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }

    public function execute()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');

        $totalQueryBuilder = clone $queryBuilder;

        try {
            $total = $totalQueryBuilder
                ->select('count(distinct(e))')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            throw new BadRequestHttpException();
        }

        $currentPage = $this->request->query->getInt('page', 1);
        $totalPages = ceil($total/$this->maxPerPage);

        if ($currentPage < 1 || $currentPage > $totalPages) {
            throw new NotFoundHttpException();
        }

        $queryBuilder->setFirstResult($this->maxPerPage * ($currentPage - 1));
        $queryBuilder->setMaxResults($this->maxPerPage);

        return $this->createResponse(['response_data' => [
            'rows'  => $queryBuilder->getQuery()->getResult(),
            'total' => $total,
            'current_page' => $currentPage,
            'total_pages' => $totalPages
        ]]);
    }

    public function requiresBefore()
    {
        return [BuildSelectQueryStep::class];
    }

}