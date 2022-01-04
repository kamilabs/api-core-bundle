<?php

namespace Kami\ApiCoreBundle\Bridge\JmsSerializer\ContextFactory;

use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\SerializationContext;
use Kami\ApiCoreBundle\Bridge\JmsSerializer\Exclusion\ApiExclusionStrategy;
use Kami\ApiCoreBundle\Security\AccessManager;

class ApiContextFactory implements SerializationContextFactoryInterface
{
    /**
     * @var AccessManager
     */
    protected  $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function createSerializationContext() : SerializationContext
    {
        return SerializationContext::create()
            ->addExclusionStrategy(new ApiExclusionStrategy($this->accessManager))
            ->enableMaxDepthChecks()
            ;
    }

}