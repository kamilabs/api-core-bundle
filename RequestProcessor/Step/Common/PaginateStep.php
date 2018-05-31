<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\Model\Pageable;
use Kami\ApiCoreBundle\Model\PageRequest;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
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

    public function execute(Request $request) : ArtifactCollection
    {
        $perPage = $request->query->getInt('per_page', $this->perPage);

        if ($perPage > $this->maxPerPage) {
            throw new BadRequestHttpException('Max per page parameter is greater than allowed');
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getArtifact('query_builder');
        $currentPage = $request->query->getInt('page', 1);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($queryBuilder));
        $paginator->setMaxPerPage($perPage);
        $paginator->setCurrentPage($currentPage);

        if ($currentPage < 1 || $currentPage > $paginator->getNbPages()) {
            throw new NotFoundHttpException();
        }
        return new ArtifactCollection([
            new Artifact(
                'response_data',
                new Pageable(
                    iterator_to_array($paginator->getIterator()),
                    $paginator->getNbResults(),
                    new PageRequest($currentPage, $paginator->getNbPages())
            )),
            new Artifact('status', 200)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['query_builder', 'select_query_built', 'access_granted'];
    }

}