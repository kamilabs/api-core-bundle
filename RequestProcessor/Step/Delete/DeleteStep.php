<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Delete;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeleteStep extends AbstractStep
{
    /**
     * @var EntityManager
     */
    protected  $manager;

    public function __construct(Registry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }


    public function execute(Request $request) : ArtifactCollection
    {
        $entity = $this->getArtifact('entity');

        try {
            $this->manager->remove($entity);
            $this->manager->flush();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Your request can not be processed', $exception);
        }

        return new ArtifactCollection([
            new Artifact('data', null),
            new Artifact('status', 204)
        ]);
    }

    public function getRequiredArtifacts() : array 
    {
        return ['entity', 'access_granted'];
    }

}