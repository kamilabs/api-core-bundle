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
     * @var array
     */
    protected $validatorMap = [
        'GET' => 'canAccessResource',
        'POST' => 'canCreateResource',
        'PUT' => 'canUpdateResource',
        'DELETE' => 'canDeleteResource'
    ];

    /**
     * ValidateResourceAccessStep constructor.
     *
     * @param AccessManager $accessManager
     */
    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        $method = $this->validatorMap[$this->request->getMethod()];
        if (!call_user_func([$this->accessManager, $method], $reflection)) {
            throw new AccessDeniedHttpException();
        }

        return new ProcessorResponse($this->request, $this->response->getData());
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }
}