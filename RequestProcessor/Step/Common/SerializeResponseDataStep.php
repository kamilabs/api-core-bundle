<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;

class SerializeResponseDataStep extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        $serializer = $this->getArtifact('serializer');

        $serialized = $serializer->serialize(
            $this->getArtifact('response_data'),
            $request->attributes->get('_format')
        );

        return new ArtifactCollection([
            new Artifact('data', $serialized)
        ]);


    }

    public function getRequiredArtifacts() : array
    {
        return ['serializer', 'response_data', 'access_granted'];
    }
}