<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Item;


use Doctrine\ORM\QueryBuilder;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;

class AddWhereStep extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getArtifact('query_builder');
        $queryBuilder->where('e.id = :id')
            ->setParameter('id', $request->get('id', 0))
            ->setMaxResults(1)
        ;

        return new ArtifactCollection([
            new Artifact('where_added', true)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['query_builder', 'select_query_built', 'access_granted'];
    }

}