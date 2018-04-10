<?php

namespace Kami\ApiCoreBundle\Form;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Kami\ApiCoreBundle\Annotation\CanBeCreatedBy;
use Kami\ApiCoreBundle\Annotation\CanBeEditedBy;
use Kami\ApiCoreBundle\Annotation\Form;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Factory
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var CachedReader
     */
    private $annotationReader;

    /**
     * @var AccessManager
     */
    private $accessManager;

    /**
     * Factory constructor.
     *
     * @param FormFactory $formFactory
     * @param CachedReader $annotationReader
     * @param AccessManager $accessManager
     */
    public function __construct(FormFactory $formFactory, Reader $annotationReader, AccessManager $accessManager)
    {
        $this->formFactory = $formFactory;
        $this->annotationReader = $annotationReader;
        $this->accessManager = $accessManager;
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCreateForm($entity)
    {
        $reflection = new \ReflectionClass($entity);
        $builder = $this->createBaseFormBuilder($entity, $reflection);
        foreach ($reflection->getProperties() as $property) {
            if ($property->getName() !== 'id' && $this->accessManager->canCreateProperty($property)) {
                $formAnnotation = $this->annotationReader->getPropertyAnnotation($property, Form::class);
                if ($formAnnotation) {
                    $builder->add($property->getName(), $formAnnotation->type, $formAnnotation->options);
                } else {
                    $builder->add($property->getName());
                }

            }
        }

        return $builder->getForm();
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getEditForm($entity)
    {
        $reflection = new \ReflectionClass($entity);
        $builder = $this->createBaseFormBuilder($entity, $reflection);
        $builder->setMethod('PUT');
        foreach ($reflection->getProperties() as $property) {
            if ($property->getName() !== 'id' && $this->accessManager->canEditProperty($property)) {
                $formAnnotation = $this->annotationReader->getPropertyAnnotation($property, Form::class);
                if ($formAnnotation) {
                    $builder->add($property->getName(), $formAnnotation->type, $formAnnotation->options);
                } else {
                    $builder->add($property->getName());
                }
            }
        }

        return $builder->getForm();
    }

    /**
     * @param $entity
     * @param $reflection
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    private function createBaseFormBuilder($entity, $reflection)
    {
        $builder = $this->formFactory->createNamedBuilder(
            Inflector::tableize($reflection->getShortName()),
            FormType::class,
            $entity,
            ['csrf_protection' => false]
        );

        return $builder;
    }
}
