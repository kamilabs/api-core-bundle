<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateResourceAccessStep extends AbstractStep
{
    /**
     * @var AccessManager
     */
    protected $accessManager;

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');

        switch ($this->request->getMethod()) {
            case 'GET':
                if (!$this->accessManager->canAccessResource($reflection)) {
                    throw new AccessDeniedHttpException();
                }
                break;
            case 'POST':
                if (!$this->accessManager->canCreateResource($reflection)) {
                    throw new AccessDeniedHttpException();
                }
                break;
            case 'PUT':
                if (!$this->accessManager->canUpdateResource($reflection)) {
                    throw new AccessDeniedHttpException();
                }
                break;
            case 'DELETE':
                if (!$this->accessManager->canDeleteResource($reflection)) {
                    throw new AccessDeniedHttpException();
                }
                break;
        }


        return new ProcessorResponse($this->request, $this->response->getData());
    }

    public function setAccessManager(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }
}