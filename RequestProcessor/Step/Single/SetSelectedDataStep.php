<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Single;

use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;


class SetSelectedDataStep extends AbstractStep
{

    public function execute()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->response['query_builder'];
        $responseData = $queryBuilder->getQuery()->getResult();

        return $this->createResponse(['response_data' => $responseData]);
    }

    public function requiresBefore()
    {
        return [AddWhereStep::class];
    }

}