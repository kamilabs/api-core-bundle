<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;

class GetQueryBuilderStep extends AbstractStep
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function execute(Request $request) : ArtifactCollection
    {
        return new ArtifactCollection([
            new Artifact('query_builder', $this->doctrine->getManager()->createQueryBuilder())
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['access_granted'];
    }
}