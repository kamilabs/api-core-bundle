<?php

namespace src\Kami\ApiCoreBundle\Security;

use Doctrine\Common\Annotations\AnnotationReader;
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
        $accessManager = new AccessManager(
            $this->mock(TokenStorage::class), new AnnotationReader()
        );
        $this->assertInstanceOf(AccessManager::class, $accessManager);
    }

//  canAccessResource

    public function testCanAccessResourceAnonymousAccess()
    {
        $tokenMock = $this->mock(AnonymousToken::class);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new AnonymousAccess()], $reflection);

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
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflection);

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
        $access->roles = ['ROLE_Admin'];
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new Access()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }
    public function testCanAccessResourceNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflection = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new CanBeEditedBy()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canAccessResource($reflection));
    }


//  canCreateResource

    public function testCanCreateResourceAnonymous()
    {
        $tokenMock = $this->mock(TokenStorage::class);
        $reflection = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(Reader::class, 'getClassAnnotations', [new AnonymousCreate()], $reflection);

        $accessManager = new AccessManager($tokenMock, $annReader);
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
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflection);

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
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new Access()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }
    public function testCanCreateResourceNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflection = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new CanBeEditedBy()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateResource($reflection));
    }

// canCreateProperty

    public function testCanCreatePropertyAnonymous()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);

        $reflection = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(Reader::class, 'getPropertyAnnotations', [new AnonymousCreate()], $reflection);

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
        $access->roles = ['ROLE_USER','ROLE_ADMIN'];
        $annReader = $this->mock(Reader::class, 'getPropertyAnnotations', [$access], $reflection);

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
        $annReader = $this->mock(Reader::class, 'getPropertyAnnotations', [$access], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflection = $this->mock(\ReflectionProperty::class);
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [new CanBeEditedBy()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }
    public function testCanCreatePropertyWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);
        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);
        $reflection = $this->mock(\ReflectionProperty::class);
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [new Access()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canCreateProperty($reflection));
    }


//  canEditResource

    public function testCanEditResourceAnonymous()
    {
        $tokenMock = $this->mock(TokenStorage::class);

        $reflectionMock = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(Reader::class, 'getClassAnnotations', [new AnonymousEdit()], $reflectionMock);

        $accessManager = new AccessManager($tokenMock, $annReader);
        $this->assertTrue($accessManager->canEditResource($reflectionMock));
    }
    public function testCanEditResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);

        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $access = new CanBeEditedBy();
        $access->roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canEditResource($reflectionPropertyMock));
    }
    public function testCanEditResourceWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);

        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $access = new CanBeEditedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [$access], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditResource($reflectionPropertyMock));
    }
    public function testCanEditResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new Access()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditResource($reflectionPropertyMock));
    }
    public function testCanEditResourceNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new CanBeCreatedBy()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditResource($reflectionPropertyMock));
    }

//  canEditProperty

    public function testCanEditPropertyAnonymous()
    {
        $tokenMock = $this->mock(TokenStorage::class);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(Reader::class, 'getPropertyAnnotations', [new AnonymousEdit()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenMock, $annReader);
        $this->assertTrue($accessManager->canEditProperty($reflectionPropertyMock));
    }
    public function testCanEditPropertyWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);

        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $access = new CanBeEditedBy();
        $access->roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [$access], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertTrue($accessManager->canEditProperty($reflectionPropertyMock));
    }
    public function testCanEditPropertyWithUserInterfaceNotCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_USER']);

        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', $userMock);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $access = new CanBeEditedBy();
        $access->roles = ['ROLE_ADMIN'];
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [$access], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditProperty($reflectionPropertyMock));
    }
    public function testCanEditPropertyWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);

        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [new Access()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditProperty($reflectionPropertyMock));
    }
    public function testCanEditPropertyNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflectionPropertyMock = $this->mock(\ReflectionProperty::class);
        $annReader = $this->mock(AnnotationReader::class, 'getPropertyAnnotations', [new CanBeCreatedBy()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canEditProperty($reflectionPropertyMock));
    }

// canDeleteResource

    public function testCanDeleteResourceAnonymous()
    {
        $tokenMock = $this->mock(TokenStorage::class);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new AnonymousDelete()], $reflection);

        $accessManager = new AccessManager($tokenMock, $annReader);
        $this->assertTrue($accessManager->canDeleteResource($reflection));
    }
    public function testCanDeleteResourceWithUserInterfaceCurrentRole()
    {
        $userMock = $this->mock(UserInterface::class, 'getRoles', ['ROLE_ADMIN']);

        $tokenMock = $this->mock(TokenInterface::class, 'getUser', $userMock);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $access = new CanBeDeletedBy();
        $access->roles = ['ROLE_USER','ROLE_ADMIN'];
        $annReader = $this->mock(Reader::class, 'getClassAnnotations', [$access], $reflection);

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
        $annReader = $this->mock(Reader::class, 'getClassAnnotations', [$access], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflection));
    }
    public function testCanDeleteResourceNotCurrentAccess()
    {
        $tokenStorageMock = $this->mock(TokenStorage::class);
        $reflectionPropertyMock = $this->mock(\ReflectionClass::class);
        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new CanBeCreatedBy()], $reflectionPropertyMock);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflectionPropertyMock));
    }
    public function testCanDeleteResourceWithoutUserInterface()
    {
        $tokenMock = $this->mock(AnonymousToken::class, 'getUser', null);

        $tokenStorageMock = $this->mock(TokenStorage::class, 'getToken', $tokenMock);

        $reflection = $this->mock(\ReflectionClass::class);

        $annReader = $this->mock(AnnotationReader::class, 'getClassAnnotations', [new Access()], $reflection);

        $accessManager = new AccessManager($tokenStorageMock, $annReader);
        $this->assertFalse($accessManager->canDeleteResource($reflection));
    }

//    public function testCanBeAccessedViaServiceContainer()
//    {
//      $client = static::createClient();
//      $accessManager = $client->getContainer()->get('kami_api_core.access_manager');
//
//      $this->assertTrue($accessManager instanceof AccessManager);
//    }

    private function mock($class, $expectedMethod = null, $willReturn = null, $methodParameter = null)
    {
        $mock = $this->createMock($class);

        if($expectedMethod){
            if($methodParameter){
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->with($methodParameter)
                    ->willReturn($willReturn);
            } else{
                $mock->expects($this->any())
                    ->method($expectedMethod)
                    ->willReturn($willReturn);
            }
        }
        return $mock;
    }

}