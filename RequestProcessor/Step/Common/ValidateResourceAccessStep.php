<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
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


    public function execute(Request $request) : ArtifactCollection
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getArtifact('reflection');
        $method = $this->validatorMap[$request->getMethod()];
        if (!call_user_func([$this->accessManager, $method], $reflection)) {
            throw new AccessDeniedHttpException();
        }

        return new ArtifactCollection([
            new Artifact('access_granted', true)
        ]);
    }

    public function getRequiredArtifacts(): array
    {
        return ['reflection'];
    }
}