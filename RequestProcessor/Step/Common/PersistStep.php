<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PersistStep extends AbstractStep
{
    /**
     * @var EntityManager
     */
    protected $manager;

    public function __construct(Registry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function execute(Request $request) : ArtifactCollection
    {
        $entity = $this->getArtifact('entity');

        if(true !== $this->getArtifact('validation')) {
            return new ArtifactCollection();
        }

        try {
            $this->manager->persist($entity);
            $this->manager->flush();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Your request can not be stored', $exception);
        }

        return new ArtifactCollection([
            new Artifact('response_data', $entity),
            new Artifact('status', 200)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['validation', 'entity', 'access_granted'];
    }


}