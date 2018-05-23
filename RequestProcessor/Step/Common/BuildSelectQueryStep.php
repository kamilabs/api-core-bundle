<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\Annotation\Relation;
use Kami\ApiCoreBundle\RequestProcessor\ProcessingException;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;

class BuildSelectQueryStep extends AbstractStep
{
    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @var Reader
     */
    protected $reader;


    public function __construct(AccessManager $accessManager, Reader $reader)
    {
        $this->accessManager = $accessManager;
        $this->reader = $reader;
    }

    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        $queryBuilder->from($reflection->getName(), 'e');
        $queryBuilder->addSelect('e');

        foreach ($reflection->getProperties() as $property) {
            $this->addJoinIfRelation($property, $queryBuilder);
        }

        return $this->createResponse(['query_builder' => $queryBuilder]);
    }


    public function requiresBefore()
    {
        return [
            GetQueryBuilderStep::class
        ];
    }

    /**
     * @param $property
     * @param $queryBuilder
     */
    protected function addJoinIfRelation(\ReflectionProperty $property, QueryBuilder $queryBuilder)
    {
        if ($this->isRelation($property) && $this->accessManager->canAccessProperty($property)) {
            $alias = Inflector::tableize($property->getName());
            $queryBuilder->leftJoin(sprintf('e.%s', $property->getName()), $alias);
            $queryBuilder->addSelect($alias);
        }

    }

    protected function isRelation(\ReflectionProperty $property)
    {
        return !empty($this->reader->getPropertyAnnotation($property, Relation::class));
    }
}