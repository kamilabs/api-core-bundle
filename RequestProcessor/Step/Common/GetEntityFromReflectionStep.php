<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;


class GetEntityFromReflectionStep extends AbstractStep
{
    public function execute()
    {
        $class = $this->getFromResponse('reflection')->getName();
        $entity = new $class;

        return $this->createResponse(['entity' => $entity]);
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }
}