<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;


class GetEntityFromReflectionStep extends AbstractStep
{
    public function execute()
    {
        $entity = new ($this->getFromResponse('reflection')->getName());

        return $this->createResponse(['entity' => $entity]);
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }
}