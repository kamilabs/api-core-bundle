<?php

namespace Kami\ApiCoreBundle\Security;

use Doctrine\Common\Annotations\Reader;
use Kami\ApiCoreBundle\Annotation\Access;
use Kami\ApiCoreBundle\Annotation\AnonymousAccess;
use Kami\ApiCoreBundle\Annotation\AnonymousCreate;
use Kami\ApiCoreBundle\Annotation\AnonymousDelete;
use Kami\ApiCoreBundle\Annotation\AnonymousUpdate;
use Kami\ApiCoreBundle\Annotation\CanBeCreatedBy;
use Kami\ApiCoreBundle\Annotation\CanBeDeletedBy;
use Kami\ApiCoreBundle\Annotation\CanBeUpdatedBy;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
    private $reader;

    private array $userRoles = [];

    public function __construct(TokenStorageInterface $tokenStorage, Reader $annotationReader)
    {
        $this->tokenStorage = $tokenStorage;
        $this->reader = $annotationReader;
        if ($tokenStorage->getToken()?->getUser() instanceof UserInterface) {
            $this->userRoles = $tokenStorage->getToken()->getUser()->getRoles();
        }
    }


    public function canAccessProperty(\ReflectionProperty $property) : bool
    {
        if ($this->reader->getPropertyAnnotation($property, AnonymousAccess::class)) {
            return true;
        }

        if ($annotation = $this->reader->getPropertyAnnotation($property, Access::class)) {
            return $this->hasRoleWithAccess($annotation);
        }

        return false;
    }

    public function canAccessResource(\ReflectionClass $reflection) : bool
    {

        if ($reflection->getAttributes(AnonymousAccess::class)|| $this->reader->getClassAnnotation($reflection, AnonymousAccess::class)) {
            return true;
        }

        if ($annotation = $this->reader->getClassAnnotation($reflection, Access::class)) {
            return $this->hasRoleWithAccess($annotation);
        }

        return false;
    }

    public function canCreateResource(\ReflectionClass $reflection) : bool
    {
        if ($this->reader->getClassAnnotation($reflection, AnonymousCreate::class)) {
            return true;
        }

        if ($annotation = $this->reader->getClassAnnotation($reflection, CanBeCreatedBy::class)) {
            return $this->hasRoleWithAccess($annotation);
        }

        return false;
    }

    public function canCreateProperty(\ReflectionProperty $property) : bool
    {
        if ($this->reader->getPropertyAnnotation($property, AnonymousCreate::class)) {
            return true;
        }

        if ($annotation = $this->reader->getPropertyAnnotation($property, CanBeCreatedBy::class)) {
            return $this->hasRoleWithAccess($annotation);
        }
        return false;
    }

    public function canUpdateResource(\ReflectionClass $reflection) : bool
    {
        if ($this->reader->getClassAnnotation($reflection, AnonymousUpdate::class)) {
            return true;
        }

        if ($annotation = $this->reader->getClassAnnotation($reflection, CanBeUpdatedBy::class)) {
            return $this->hasRoleWithAccess($annotation);
        }

        return false;
    }

    public function canUpdateProperty(\ReflectionProperty $property) : bool
    {
        if ($this->reader->getPropertyAnnotation($property, AnonymousUpdate::class)) {
            return true;
        }

        if ($annotation = $this->reader->getPropertyAnnotation($property, CanBeUpdatedBy::class)) {
            return $this->hasRoleWithAccess($annotation);
        }
        return false;
    }

    public function canDeleteResource(\ReflectionClass $reflection) : bool
    {
        if ($this->reader->getClassAnnotation($reflection, AnonymousDelete::class)) {
            return true;
        }

        if ($annotation = $this->reader->getClassAnnotation($reflection, CanBeDeletedBy::class)) {
            return $this->hasRoleWithAccess($annotation);
        }

        return false;
    }

    private function hasRoleWithAccess($annotation) : bool
    {
        return count(array_intersect($annotation->roles, $this->userRoles)) > 0;
    }
}
