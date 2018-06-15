<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class FetchEntityByIdStep extends AbstractStep
{
    /**
     * @var Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function execute(Request $request) : ArtifactCollection
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getArtifact('reflection');
        $entity = $this->doctrine->getRepository($reflection->getName())->find($request->get('id', 0));

        if (!$entity) {
            throw new NotFoundHttpException('Resource not found');
        }

        return new ArtifactCollection([
            new Artifact('entity', $entity)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['reflection', 'access_granted'];
    }

}