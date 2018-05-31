<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Kami\ApiCoreBundle\Annotation\Form;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\Component\RequestProcessor\ProcessingException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Kami\Component\RequestProcessor\Step\AbstractStep;

abstract class AbstractBuildFormStep extends AbstractStep
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * AbstractBuildFormStep constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param AccessManager $accessManager
     * @param Reader $reader
     */
    public function __construct(FormFactoryInterface $formFactory, AccessManager $accessManager, Reader $reader)
    {
        $this->formFactory = $formFactory;
        $this->accessManager = $accessManager;
        $this->reader = $reader;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'generic_build_form_step';
    }

    /**
     * @param $method
     *
     * @throws ProcessingException
     *
     * @return FormBuilderInterface
     */
    protected function getBaseFormBuilder($method) : FormBuilderInterface
    {
        $builder = $this->formFactory->createNamedBuilder(
            Inflector::tableize($this->getArtifact('reflection')->getShortName()),
            FormType::class,
            $this->getArtifact('entity'),
            ['csrf_protection' => false]
        );

        if ('PUT' === $method) {
            $builder->setMethod('PUT');
        }

        return $builder;
    }

    /**
     * @param \ReflectionProperty $property
     * @param FormBuilderInterface $builder
     */
    protected function addField(\ReflectionProperty $property, FormBuilderInterface $builder) : void
    {
        if ($annotation = $this->reader->getPropertyAnnotation($property, Form::class)) {
            $builder->add(Inflector::tableize($property->getName()), $annotation->type, $annotation->options);
        } else {
            $builder->add(Inflector::tableize($property->getName()));
        }
    }
}