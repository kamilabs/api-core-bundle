<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Kami\ApiCoreBundle\Annotation\Form;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

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
    public function getName()
    {
        return 'generic_build_form_step';
    }

    /**
     * @return FormBuilderInterface
     */
    protected function getBaseFormBuilder()
    {
        $builder = $this->formFactory->createNamedBuilder(
            Inflector::tableize($this->getFromResponse('reflection')->getShortName()),
            FormType::class,
            $this->getFromResponse('entity'),
            ['csrf_protection' => false]
        );

        if ('PUT' === $this->request->getMethod()) {
            $builder->setMethod('PUT');
        }

        return $builder;
    }

    /**
     * @param \ReflectionProperty $property
     * @param FormBuilderInterface $builder
     */
    protected function addField(\ReflectionProperty $property, FormBuilderInterface $builder)
    {
        if ($annotation = $this->reader->getPropertyAnnotation($property, Form::class)) {
            $builder->add(Inflector::tableize($property->getName()), $annotation->type, $annotation->options);
        } else {
            $builder->add(Inflector::tableize($property->getName()));
        }
    }
}