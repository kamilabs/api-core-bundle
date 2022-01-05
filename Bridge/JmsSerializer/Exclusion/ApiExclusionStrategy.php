<?php


namespace Kami\ApiCoreBundle\Bridge\JmsSerializer\Exclusion;


use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Kami\ApiCoreBundle\Model\Pageable;
use Kami\ApiCoreBundle\Model\PageRequest;
use Kami\ApiCoreBundle\Security\AccessManager;

class ApiExclusionStrategy implements ExclusionStrategyInterface
{
    private $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function shouldSkipClass(ClassMetadata $metadata, Context $context) : bool
    {
        if (in_array($metadata->name, [Pageable::class, PageRequest::class])) {
            return false;
        }
        
        return !$this->accessManager->canAccessResource(new \ReflectionClass($metadata->name));
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context) : bool
    {
        if (in_array($property->class, [Pageable::class, PageRequest::class])) {
            return false;
        }
       
        return !$this->accessManager->canAccessProperty(new \ReflectionProperty($property->class, $property->name));
    }
    
}