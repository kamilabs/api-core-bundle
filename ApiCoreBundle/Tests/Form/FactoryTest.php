<?php

namespace src\Kami\ApiCoreBundle\Form;

use Doctrine\Common\Annotations\Reader;
use Kami\ApiCoreBundle\Form\Factory;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactoryTest extends WebTestCase
{
    public function testCanBeConstructedWithNecessaryParameters()
    {
        $formFactory = $this->mock(FormFactory::class);
        $reader = $this->mock(Reader::class);
        $accessManager = $this->mock(AccessManager::class);

        $factory = new Factory($formFactory, $reader, $accessManager);

        $this->assertInstanceOf(Factory::class, $factory);
    }

    public function testFactoryGetCreateForm()
    {
        $entity = new MyModel();

        $builder = $this->mock(FormBuilder::class, 'getForm', 'OK');
        $reader = $this->mock(Reader::class);
        $formFactory = $this->mock(FormFactory::class, 'createNamedBuilder', $builder);
        $accessManager = $this->mock(AccessManager::class);

        $factory = new Factory($formFactory, $reader, $accessManager);

        $this->assertEquals('OK',  $factory->getCreateForm($entity));
    }

    public function testFactoryGetEditForm()
    {
        $entity = new MyModel();

        $builder = $this->mock(FormBuilder::class, 'getForm', 'OK');
        $reader = $this->mock(Reader::class);
        $formFactory = $this->mock(FormFactory::class, 'createNamedBuilder', $builder);
        $accessManager = $this->mock(AccessManager::class, 'canCreateProperty', true);

        $factory = new Factory($formFactory, $reader, $accessManager);

        $this->assertEquals('OK',  $factory->getEditForm($entity));
    }

    private function mock($class, $expectedMethod = null, $willReturn = null, $methodParameter = null)
    {
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

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