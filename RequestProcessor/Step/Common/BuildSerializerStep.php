<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\Bridge\JmsSerializer\ContextFactory\ApiContextFactory;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;


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
        $this->serializer = $serializer;
    }

    public function execute()
    {
        $this->serializer
            ->setSerializationContextFactory(new ApiContextFactory($this->accessManager));

        return $this->createResponse(['serializer' => $this->serializer]);
    }

    public function requiresBefore()
    {
        return [];
    }

}