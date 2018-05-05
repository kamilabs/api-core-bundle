<?php

namespace src\Kami\ApiCoreBundle\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\PropertyMetadata;
use Kami\ApiCoreBundle\Annotation\Access;
use Kami\ApiCoreBundle\Annotation\AnonymousAccess;
use Kami\ApiCoreBundle\Annotation\AnonymousCreate;
use Kami\ApiCoreBundle\Annotation\AnonymousDelete;
use Kami\ApiCoreBundle\Annotation\AnonymousUpdate;
use Kami\ApiCoreBundle\Annotation\CanBeCreatedBy;
use Kami\ApiCoreBundle\Annotation\CanBeDeletedBy;
use Kami\ApiCoreBundle\Annotation\CanBeUpdatedBy;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessManagerTest extends WebTestCase
{
// can be constructed with necessary params

    public function testCanBeConstructedWithNecessaryParams()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER', 'ROLE_ADMIN']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $accessManager = new AccessManager(
            $tokenStorageMock, $this->mock(AnnotationReader::class)
        );
        $this->assertInstanceOf(AccessManager::class, $accessManager);
    }

//  canAccessResource

    public function testCanAccessResourceAnonymousAccess()
    {
        $tokenMock = $this->mock(AnonymousToken::class);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', [new AnonymousAccess()]);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER', 'ROLE_ADMIN']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflection = $this->mock(\ReflectionClass::class);

        $access = new Access();
        $access->roles = ['ROLE_USER'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflection = $this->mock(\ReflectionClass::class);

        $access = new Access();
        $access->roles = ['ROLE_ADMIN'];

        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceWithoutUserInterface()
    {
        $token = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorage = $this->mock(TokenStorage::class, 'getToken', $token);
        $reflection = $this->mock(\ReflectionClass::class);

        $access = new Access();
        $access->roles = ['ROLE_ADMIN'];
        $reader = $this->createMock(AnnotationReader::class);
        $reader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $reader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorage, $reader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceNotCurrentAccess()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new Access();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }


//  canCreateResource

    public function testCanCreateResourceAnonymous()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(Reader::class, 'getClassAnnotation', [new AnonymousCreate()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeCreatedBy();
        $access->roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeCreatedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeCreatedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceNotCurrentAccess()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation');

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }

// canCreateProperty

    public function testCanCreatePropertyAnonymous()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(Reader::class, 'getPropertyAnnotation', [new AnonymousCreate()]);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionProperty::class);

        $access = new CanBeCreatedBy();
        $access->roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getPropertyAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionProperty::class);

        $access = new CanBeCreatedBy();
        $access->roles = ['ROLE_USER'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getPropertyAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyNotCurrentAccess()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflection = $this->mock(\ReflectionProperty::class);
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }


//  canUpdateResource

    public function testCanUpdateResourceAnonymous()
    {
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionMock = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(Reader::class, 'getClassAnnotation', true);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canUpdateResource($reflectionMock));
    }
    public function testCanUpdateResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $access = new CanBeUpdatedBy();
        $access->roles = ['ROLE_USER'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canUpdateResource($reflectionPropertyMock));
    }
    public function testCanUpdateResourceWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $access = new CanBeUpdatedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateResource($reflectionPropertyMock));
    }
    public function testCanUpdateResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateResource($reflectionPropertyMock));
    }
    public function testCanUpdateResourceNotCurrentAccess()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateResource($reflectionPropertyMock));
    }

//  canUpdateProperty

    public function testCanUpdatePropertyAnonymous()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(Reader::class, 'getPropertyAnnotation', [new AnonymousUpdate()]);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canUpdateProperty($reflectionPropertyMock));
    }
    public function testCanUpdatePropertyWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $access = new CanBeUpdatedBy();
        $access->roles = ['ROLE_USER'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getPropertyAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canUpdateProperty($reflectionPropertyMock));
    }
    public function testCanUpdatePropertyWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $access = new CanBeUpdatedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getPropertyAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getPropertyAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateProperty($reflectionPropertyMock));
    }
    public function testCanUpdatePropertyWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateProperty($reflectionPropertyMock));
    }
    public function testCanUpdatePropertyNotCurrentAccess()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canUpdateProperty($reflectionPropertyMock));
    }

// canDeleteResource

    public function testCanDeleteResourceAnonymous()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', new AnonymousDelete());

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canDeleteResource($reflection));
    }

    public function testCanDeleteResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeDeletedBy();
        $access->roles = ['ROLE_ADMIN'];

        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canDeleteResource($reflection));
    }

    public function testCanDeleteResourceWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeDeletedBy();
        $access->roles = ['ROLE_USER'];
        $annReader = $this->createMock(AnnotationReader::class);
        $annReader->expects($this->at(0))
            ->method('getClassAnnotation')
            ->willReturn(null);
        $annReader->expects($this->at(1))
            ->method('getClassAnnotation')
            ->willReturn($access);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflection));
    }

    public function testCanDeleteResourceNotCurrentAccess()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);
        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflectionPropertyMock));
    }

    public function testCanDeleteResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotation', null);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflection));
    }

    private function mock($class, $expectedMethod = null, $willReturn = null, $methodParameter = null)
    {
        $mock = $this->createMock($class);

        if ($expectedMethod) {
            if ($methodParameter) {
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->with($methodParameter)
                    ->willReturn($willReturn);
            } else {
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->willReturn($willReturn);
            }
        }
        return $mock;
    }

}