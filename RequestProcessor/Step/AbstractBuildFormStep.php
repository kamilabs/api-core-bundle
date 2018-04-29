<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

abstract class AbstractBuildFormStep extends AbstractStep
{
    /**
     * @var FormFactory
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
     * @param FormFactory $formFactory
     */
    public function setFormFactory(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param AccessManager $accessManager
     */
    public function setAccessManager($accessManager)
    {
        $this->accessManager = $accessManager;
    }

    /**
     * @param Reader $reader
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
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

    public function getName()
    {
        return 'generic_build_form_step';
    }
}