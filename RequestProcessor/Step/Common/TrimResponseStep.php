<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;

class TrimResponseStep extends AbstractStep
{
    protected $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        $persistedEntity = $this->getFromResponse('response_data');

        $data = [];

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canAccessProperty($property)) {
                $data[$property->getName()] = call_user_func([$persistedEntity, 'get'.ucfirst($property->getName())]);
            }
        }

        return $this->createResponse(['response_data' => $data]);
    }

    public function requiresBefore()
    {
        return [PersistStep::class];
    }
}