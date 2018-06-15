<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\Annotation\Relation;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\HttpFoundation\Request;

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

    public function execute(Request $request) : ArtifactCollection
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getArtifact('reflection');
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getArtifact('query_builder');
        $queryBuilder->from($reflection->getName(), 'e');
        $queryBuilder->addSelect('e');

        foreach ($reflection->getProperties() as $property) {
            $this->addJoinIfRelation($property, $queryBuilder);
        }

        return new ArtifactCollection([
            new Artifact('select_query_built', true)
        ]);
    }


    public function getRequiredArtifacts() : array
    {
        return ['reflection', 'query_builder', 'access_granted'];
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