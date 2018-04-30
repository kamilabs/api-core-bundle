<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;

class BuildSelectQueryStep extends AbstractStep
{
    /**
     * @var AccessManager
     */
    private $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        $queryBuilder->from($reflection->getName(), 'e');

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canAccessProperty($property)) {
                $queryBuilder
                    ->addSelect('e.'.$property->getName());
            }
        }

        return $this->createResponse(['query_builder' => $queryBuilder]);
    }


    public function requiresBefore()
    {
        return [
            GetReflectionFromRequestStep::class,
            ValidateResourceAccessStep::class,
            GetQueryBuilderStep::class
        ];
    }
}