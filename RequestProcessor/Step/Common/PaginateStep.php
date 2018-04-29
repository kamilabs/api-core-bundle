<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class PaginateStep
 * @package Kami\ApiCoreBundle\RequestProcessor\Step\Common
 */
class PaginateStep extends AbstractStep
{
    protected $maxPerPage;

    public function execute()
    {
        $adapter = new DoctrineORMAdapter($this->getFromResponse('query_builder'));
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->maxPerPage);
        $pagerfanta->setCurrentPage($this->request->query->getInt('page', 1));

        return $this->createResponse(['paginator' => $pagerfanta]);
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }

    public function requiresBefore()
    {
        return [BuildSelectQueryStep::class];
    }

}