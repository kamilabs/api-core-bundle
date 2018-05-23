<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Create;


use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractBuildFormStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;



class BuildCreateFormStep extends AbstractBuildFormStep
{
    public function execute()
    {
        $builder = $this->getBaseFormBuilder();
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canCreateProperty($property)) {
                $this->addField($property, $builder);
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