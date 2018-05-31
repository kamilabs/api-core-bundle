<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;


class GetEntityFromReflectionStep extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        $class = $this->getArtifact('reflection')->getName();
        $entity = new $class;

        return new ArtifactCollection([
            new Artifact('entity', $entity)
        ]);
    }

    public function getRequiredArtifacts(): array
    {
        return ['reflection', 'access_granted'];
    }
}