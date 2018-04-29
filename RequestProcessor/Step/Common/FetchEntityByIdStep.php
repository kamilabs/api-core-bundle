<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;


class FetchEntityByIdStep extends AbstractStep
{
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }

}