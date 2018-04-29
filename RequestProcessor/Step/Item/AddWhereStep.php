<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Item;


use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;

class AddWhereStep extends AbstractStep
{
    public function execute()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        $queryBuilder->where('e.id = :id')
            ->setParameter('id', $this->request->get('id'))
            ->setMaxResults(1)
        ;

        return $this->createResponse(['query_builder' => $queryBuilder]);
    }

    public function requiresBefore()
    {
        return [BuildSelectQueryStep::class];
    }

}