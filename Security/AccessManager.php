<?php

namespace Kami\ApiCoreBundle\Security;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\PropertyMetadata;
use Kami\ApiCoreBundle\Annotation\Access;
use Kami\ApiCoreBundle\Annotation\AnonymousAccess;
use Kami\ApiCoreBundle\Annotation\AnonymousCreate;
use Kami\ApiCoreBundle\Annotation\AnonymousDelete;
use Kami\ApiCoreBundle\Annotation\AnonymousEdit;
use Kami\ApiCoreBundle\Annotation\CanBeCreatedBy;
use Kami\ApiCoreBundle\Annotation\CanBeDeletedBy;
use Kami\ApiCoreBundle\Annotation\CanBeEditedBy;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessManager
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Reader

     */
    private $annotationReader;

    /**
     * AccessManager constructor.
     *
     * @param TokenStorage $tokenStorage
     * @param Reader $annotationReader
     */
    public function __construct(TokenStorage $tokenStorage, Reader $annotationReader)
    {
        $this->tokenStorage = $tokenStorage;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param $entity
     * @param $context
     * @param PropertyMetadata $metadata
     * @return bool
     */
    public function canAccessProperty($entity, $context, PropertyMetadata $metadata)
    {
        foreach ($this->annotationReader->getPropertyAnnotations($metadata->reflection) as $annotation) {
            if ($annotation instanceof AnonymousAccess) {
                return true;
            }
            if ($annotation instanceof Access && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }
        return false;
    }

    /**
     * @param \ReflectionClass $entity
     * @return bool
     */
    public function canAccessResource(\ReflectionClass $entity)
    {
        foreach ($this->annotationReader->getClassAnnotations($entity) as $annotation) {
            if ($annotation instanceof AnonymousAccess) {
                return true;
            }
            if ($annotation instanceof Access && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }

        return false;
    }

    public function canCreateResource(\ReflectionClass $entity)
    {
        foreach ($this->annotationReader->getClassAnnotations($entity) as $annotation) {
            if ($annotation instanceof AnonymousCreate) {
                return true;
            }
            if ($annotation instanceof CanBeCreatedBy
                && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }

        return false;
    }

    public function canCreateProperty(\ReflectionProperty $property)
    {
        foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
            if ($annotation instanceof AnonymousCreate) {
                return true;
            }
            if ($annotation instanceof CanBeCreatedBy
                && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }

        return false;
    }

    public function canEditResource(\ReflectionClass $entity)
    {
        foreach ($this->annotationReader->getClassAnnotations($entity) as $annotation) {
            if ($annotation instanceof AnonymousEdit) {
                return true;
            }
            if ($annotation instanceof CanBeEditedBy
                && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }
        return false;
    }

    public function canEditProperty(\ReflectionProperty $property)
    {
        foreach ($this->annotationReader->getPropertyAnnotations($property) as $annotation) {
            if ($annotation instanceof AnonymousEdit) {
                return true;
            }
            if ($annotation instanceof CanBeEditedBy
                && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }

        return false;
    }

    public function canDeleteResource(\ReflectionClass $entity)
    {
        foreach ($this->annotationReader->getClassAnnotations($entity) as $annotation) {
            if ($annotation instanceof AnonymousDelete) {
                return true;
            }
            if ($annotation instanceof CanBeDeletedBy
                && $this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
                return count(
                        array_intersect(
                            $annotation->roles,
                            $this->tokenStorage->getToken()->getUser()->getRoles()
                        )
                    ) > 0;
            }
        }
        return false;
    }
}
