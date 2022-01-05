<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Kami\ApiCoreBundle\Bridge\JmsSerializer\ContextFactory\ApiContextFactory;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;


class BuildSerializerStep extends AbstractStep
{
    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(AccessManager $accessManager, Serializer $serializer)
    {
        $this->accessManager = $accessManager;
    }

    public function execute(Request $request) : ArtifactCollection
    {
        $serializer = SerializerBuilder::create()
            ->setSerializationContextFactory(new ApiContextFactory($this->accessManager))
            ->build()
            ;
        

        return new ArtifactCollection([
            new Artifact('serializer', $serializer)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['access_granted'];
    }

}