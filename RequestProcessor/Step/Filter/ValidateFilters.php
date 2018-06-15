<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Filter;


use Kami\ApiCoreBundle\Filter\Validator;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;

class ValidateFilters extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        $validator = new Validator($request);

        return new ArtifactCollection([
            new Artifact('filters', $validator->getFilters())
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['access_granted'];
    }

}