<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Item;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class SetSelectedDataStep extends AbstractStep
{

    public function execute()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        try {
            $responseData = $queryBuilder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException $exception) {
            throw new BadRequestHttpException();
        } catch (NoResultException $exception) {
            throw new NotFoundHttpException();
        }

        return $this->createResponse(['response_data' => $responseData]);
    }

    public function requiresBefore()
    {
        return [AddWhereStep::class];
    }

}