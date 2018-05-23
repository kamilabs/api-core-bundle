<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Update;


use Kami\ApiCoreBundle\Annotation\Form;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractBuildFormStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;

class BuildUpdateFormStep extends AbstractBuildFormStep
{
    public function execute()
    {
        $builder = $this->getBaseFormBuilder();
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canUpdateProperty($property)) {
                $this->addField($property, $builder);
            }
        }

        return $this->createResponse(['form' => $builder->getForm()]);
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class, ValidateResourceAccessStep::class];
    }

}