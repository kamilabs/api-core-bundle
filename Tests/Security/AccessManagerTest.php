<?php

namespace Kami\ApiCoreBundle\Tests\Security;

use Doctrine\Common\Annotations\Reader;
use Kami\ApiCoreBundle\Security\AccessManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AccessManagerTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $accessManager = new AccessManager(
            $this->createMock(TokenStorage::class),
            $this->createMock(Reader::class)
        );

        $this->assertInstanceOf(AccessManager::class, $accessManager);
    }


    public function testCanUpdateProperty()
    {

    }

    public function testCanCreateProperty()
    {

    }

    public function testCanDeleteResource()
    {

    }

    public function testCanCreateResource()
    {

    }


    public function testCanAccessProperty()
    {

    }

    public function testCanAccessResource()
    {

    }

    public function testCanUpdateResource()
    {

    }
}
