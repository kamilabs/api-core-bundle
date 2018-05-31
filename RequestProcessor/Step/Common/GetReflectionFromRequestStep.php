<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;



class GetReflectionFromRequestStep extends AbstractStep
{

    /**
     * @param Request $request
     * @return ArtifactCollection
     * @throws \ReflectionException
     */
    public function execute(Request $request) : ArtifactCollection
    {
        return new ArtifactCollection([
            new Artifact('reflection', new \ReflectionClass($request->attributes->get('_entity')))
        ]);
    }

    public function getRequiredArtifacts(): array
    {
        return [];
    }
}