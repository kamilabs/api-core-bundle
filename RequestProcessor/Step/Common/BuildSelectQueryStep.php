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
    private $accessManager;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    protected $aliases = [];

    private $accessible = [];

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

        foreach ($reflection->getProperties() as $property) {
            $this->addSelectIfEligible($property, $queryBuilder);
        }
        $queryBuilder->addSelect(sprintf('partial e.{id, %s}', implode(', ', $this->accessible)));

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
    protected function addSelectIfEligible(\ReflectionProperty $property, QueryBuilder $queryBuilder)
    {
        if ($this->accessManager->canAccessProperty($property)) {
            if (!$this->isRelation($property)) {
                $this->accessible[] = $property->getName();
                return;
            }
            $this->join($property, $queryBuilder);
        }

    }

    protected function isRelation(\ReflectionProperty $property)
    {
        return (
            $this->reader->getPropertyAnnotation($property, Relation::class) ||
            $this->reader->getPropertyAnnotation($property, OneToOne::class) ||
            $this->reader->getPropertyAnnotation($property, OneToMany::class) ||
            $this->reader->getPropertyAnnotation($property, ManyToOne::class) ||
            $this->reader->getPropertyAnnotation($property, ManyToMany::class)
        );
    }

    protected function join(\ReflectionProperty $property, QueryBuilder $queryBuilder)
    {
        $target = $this->getTarget($property);
        $alias = Inflector::tableize($property->getName());
        $queryBuilder->join(sprintf('e.%s', $property->getName()), $alias);
        $accessible = [];

        foreach ($target->getProperties() as $property) {
            if ($this->accessManager->canAccessProperty($property)) {
                $accessible[] = $property->getName();
            }
        }

        $queryBuilder->addSelect(sprintf('partial %s.{%s}', $alias, implode(',', $accessible)));
    }

    protected function getTarget(\ReflectionProperty $property)
    {
        $target = '';

        $relation = $this->reader->getPropertyAnnotation($property, Relation::class);
        if ($relation->target) {
            $target = $relation->target;
        } else {
            foreach ([OneToOne::class, OneToMany::class, ManyToOne::class, ManyToMany::class] as $possibility) {
                if ($annotation = $this->reader->getPropertyAnnotation($property, $possibility)) {
                    $target = $annotation->targetEntity;
                    break;
                }
            }
        }

        try {
            return new \ReflectionClass($target);
        } catch (\ReflectionException $e) {
            throw new ProcessingException(sprintf(
                'Could not find target entity for relation %s', $property->getName())
            );
        }
    }
}