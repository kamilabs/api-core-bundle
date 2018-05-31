<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Item;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ExecuteQueryStep extends AbstractStep
{

    public function execute(Request $request) : ArtifactCollection
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getArtifact('query_builder');
        try {
            $responseData = $queryBuilder->getQuery()->getSingleResult();
        } catch (NonUniqueResultException $exception) {
            throw new BadRequestHttpException();
        } catch (NoResultException $exception) {
            throw new NotFoundHttpException();
        }

        return new ArtifactCollection([
            new Artifact('status', 200),
            new Artifact('response_data', $responseData)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['query_builder', 'select_query_built', 'where_added'];
    }

}