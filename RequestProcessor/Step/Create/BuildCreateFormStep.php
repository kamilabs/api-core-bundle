<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Create;

use Doctrine\Common\Annotations\Reader;
use Kami\ApiCoreBundle\Annotation\Form;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractBuildFormStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\Form\FormFactoryInterface;


class BuildCreateFormStep extends AbstractBuildFormStep
{
    public function execute()
    {
        $builder = $this->getBaseFormBuilder();
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canCreateProperty($property)) {
                if ($annotation = $this->reader->getPropertyAnnotation($property, Form::class)) {
                    $builder->add($property->getName(), $annotation->type, $annotation->options);
                }
                $builder->add($property->getName());
            }
        }

        return $this->createResponse(['form' => $builder->getForm()]);
    }


    public function requiresBefore()
    {
        return [
            ValidateResourceAccessStep::class,
            GetEntityFromReflectionStep::class,
            GetReflectionFromRequestStep::class
        ];
    }
}