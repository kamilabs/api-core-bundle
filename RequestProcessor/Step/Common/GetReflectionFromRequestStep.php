<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class GetReflectionFromRequestStep extends AbstractStep
{

    /**
     * @throws NotFoundHttpException
     * @return ProcessorResponse|\Kami\ApiCoreBundle\RequestProcessor\ResponseInterface
     */
    public function execute()
    {
        try {
            return $this->createResponse(
                ['reflection' => new \ReflectionClass($this->request->attributes->get('_entity'))]
            );

        } catch (\ReflectionException $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @return array
     */
    public function requiresBefore()
    {
        return [];
    }
}